<?php

/**
 * Class PublicShareController
 *
 * Serves read-only public views of locations and plants via share tokens.
 * Intentionally does NOT extend BaseController so auth is never enforced.
 */
class PublicshareController extends Asatru\Controller\Controller {
    const PUBLIC_LAYOUT = 'public_layout';

    /**
     * Perform base initialization (no auth check)
     *
     * @return void
     */
    public function __construct()
    {
        app_mail_config();
        app_set_timezone();
    }

    /**
     * Check that public sharing is globally enabled, abort with 403 otherwise.
     *
     * @return void
     */
    private function requireSharingEnabled()
    {
        if (!app('enable_public_sharing', false)) {
            http_response_code(403);
            exit('Public sharing is disabled.');
        }
    }

    /**
     * Handles URL: /public/share/token/{token}
     *
     * Dispatches to the correct view (collection or plant) based on the token type.
     *
     * @param Asatru\Controller\ControllerArg $request
     * @return Asatru\View\ViewHandler
     */
    public function view_shared($request)
    {
        $this->requireSharingEnabled();

        $token = $request->arg('token');

        $share = PublicShareModel::getByToken($token);
        if (!$share) {
            http_response_code(404);
            exit('Share link not found.');
        }

        if ($share->get('type') === PublicShareModel::TYPE_LOCATION) {
            return $this->view_location($share->get('entity_id'));
        } elseif ($share->get('type') === PublicShareModel::TYPE_PLANT) {
            return $this->view_plant($share->get('entity_id'));
        }

        http_response_code(404);
        exit('Unknown share type.');
    }

    /**
     * Render a read-only collection view for a location.
     *
     * @param int $location_id
     * @return Asatru\View\ViewHandler
     */
    private function view_location($location_id)
    {
        $location_data = LocationsModel::getLocationById($location_id);
        if (!$location_data) {
            http_response_code(404);
            exit('Location not found.');
        }

        $plants = PlantsModel::getAll($location_id);
        $location_share = PublicShareModel::getByEntity(PublicShareModel::TYPE_LOCATION, $location_id);

        return view(self::PUBLIC_LAYOUT, ['content', 'public_collection'], [
            'location_data' => $location_data,
            'plants' => $plants,
            'location_token' => $location_share ? $location_share->get('token') : null,
        ]);
    }

    /**
     * Render a read-only plant detail view.
     *
     * @param int $plant_id
     * @return Asatru\View\ViewHandler
     */
    private function view_plant($plant_id)
    {
        $plant = PlantsModel::getDetails($plant_id);
        if (!$plant) {
            http_response_code(404);
            exit('Plant not found.');
        }

        $photos = PlantPhotoModel::getPlantGallery($plant_id);
        $custom_attributes = CustPlantAttrModel::getForPlant($plant_id);
        $plant_log_entries = PlantLogModel::getLogEntries($plant_id);

        $tagstr = $plant->get('tags');
        if ($tagstr && substr($tagstr, strlen($tagstr) - 1, 1) !== ' ') {
            $tagstr .= ' ';
        }
        $tags = $tagstr ? array_filter(explode(' ', $tagstr)) : [];

        $plant_share = PublicShareModel::getByEntity(PublicShareModel::TYPE_PLANT, $plant_id);
        $location_share = PublicShareModel::getByEntity(PublicShareModel::TYPE_LOCATION, $plant->get('location'));

        return view(self::PUBLIC_LAYOUT, ['content', 'public_plant'], [
            'plant' => $plant,
            'photos' => $photos,
            'tags' => $tags,
            'custom_attributes' => $custom_attributes,
            'plant_log_entries' => $plant_log_entries,
            'plant_token' => $plant_share ? $plant_share->get('token') : null,
            'location_token' => $location_share ? $location_share->get('token') : null,
        ]);
    }

    /**
     * Handles URL: /public/share/log/fetch
     *
     * AJAX endpoint to paginate plant log entries on the public plant view.
     *
     * @param Asatru\Controller\ControllerArg $request
     * @return Asatru\View\JsonHandler
     */
    public function fetch_log($request)
    {
        $this->requireSharingEnabled();

        try {
            // Verify the token belongs to this plant so we don't expose arbitrary logs
            $token = $request->params()->query('token', null);
            $paginate = $request->params()->query('paginate', null);

            if (!$token) {
                throw new \Exception('Missing token');
            }

            $share = PublicShareModel::getByToken($token);
            if (!$share || $share->get('type') !== PublicShareModel::TYPE_PLANT) {
                throw new \Exception('Invalid token');
            }

            $plant_id = $share->get('entity_id');
            $entries = PlantLogModel::getLogEntries($plant_id, $paginate);

            $result = [];
            foreach ($entries as $entry) {
                $result[] = [
                    'id'         => $entry->get('id'),
                    'content'    => $entry->get('content'),
                    'created_at' => date('Y-m-d', strtotime($entry->get('created_at'))),
                    'updated_at' => date('Y-m-d', strtotime($entry->get('updated_at'))),
                ];
            }

            return json([
                'code' => 200,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Authenticated endpoints: generate / revoke share tokens
    // These require an active session.
    // -------------------------------------------------------------------------

    /**
     * Ensure the caller is authenticated, abort otherwise.
     *
     * @return mixed The authenticated user object
     */
    private function requireAuth()
    {
        $user = UserModel::getAuthUser();
        if (!$user) {
            http_response_code(403);
            header('Content-Type: application/json');
            exit(json_encode(['code' => 403, 'msg' => 'Authentication required']));
        }
        return $user;
    }

    /**
     * Handles URL: /public/share/create  (POST)
     *
     * Body params: type (location|plant), entity_id
     * Returns JSON with share_url.
     *
     * @param Asatru\Controller\ControllerArg $request
     * @return Asatru\View\JsonHandler
     */
    public function create_share($request)
    {
        $this->requireSharingEnabled();
        $this->requireAuth();

        try {
            $type = $request->params()->query('type', null);
            $entity_id = (int)$request->params()->query('entity_id', 0);

            if (!in_array($type, [PublicShareModel::TYPE_LOCATION, PublicShareModel::TYPE_PLANT])) {
                throw new \Exception('Invalid type');
            }
            if ($entity_id <= 0) {
                throw new \Exception('Invalid entity_id');
            }

            $token = PublicShareModel::getOrCreate($type, $entity_id);
            $url = url('/public/share/token/' . $token);

            return json([
                'code'      => 200,
                'token'     => $token,
                'share_url' => $url,
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handles URL: /public/share/revoke  (POST)
     *
     * Body params: type, entity_id
     *
     * @param Asatru\Controller\ControllerArg $request
     * @return Asatru\View\JsonHandler
     */
    public function revoke_share($request)
    {
        $this->requireSharingEnabled();
        $this->requireAuth();

        try {
            $type = $request->params()->query('type', null);
            $entity_id = (int)$request->params()->query('entity_id', 0);

            if (!in_array($type, [PublicShareModel::TYPE_LOCATION, PublicShareModel::TYPE_PLANT])) {
                throw new \Exception('Invalid type');
            }
            if ($entity_id <= 0) {
                throw new \Exception('Invalid entity_id');
            }

            PublicShareModel::remove($type, $entity_id);

            return json(['code' => 200]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg'  => $e->getMessage(),
            ]);
        }
    }
}
