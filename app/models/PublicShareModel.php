<?php

/**
 * Class PublicShareModel
 *
 * Manages public share tokens for locations and individual plants
 */
class PublicShareModel extends \Asatru\Database\Model {
    const TYPE_LOCATION = 'location';
    const TYPE_PLANT = 'plant';

    /**
     * Generate a unique random token
     *
     * @return string
     */
    public static function generateToken()
    {
        return bin2hex(random_bytes(24));
    }

    /**
     * Create or retrieve an existing share token for an entity
     *
     * @param string $type  'location' or 'plant'
     * @param int    $entity_id
     * @return string  The share token
     * @throws \Exception
     */
    public static function getOrCreate($type, $entity_id)
    {
        try {
            $existing = static::raw(
                'SELECT * FROM `@THIS` WHERE type = ? AND entity_id = ? LIMIT 1',
                [$type, $entity_id]
            )->first();

            if ($existing) {
                return $existing->get('token');
            }

            $token = static::generateToken();

            static::raw(
                'INSERT INTO `@THIS` (type, entity_id, token) VALUES(?, ?, ?)',
                [$type, $entity_id, $token]
            );

            return $token;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Retrieve a share record by token
     *
     * @param string $token
     * @return mixed|null
     * @throws \Exception
     */
    public static function getByToken($token)
    {
        try {
            return static::raw(
                'SELECT * FROM `@THIS` WHERE token = ? LIMIT 1',
                [$token]
            )->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Retrieve a share record by type + entity_id (to check existence)
     *
     * @param string $type
     * @param int    $entity_id
     * @return mixed|null
     * @throws \Exception
     */
    public static function getByEntity($type, $entity_id)
    {
        try {
            return static::raw(
                'SELECT * FROM `@THIS` WHERE type = ? AND entity_id = ? LIMIT 1',
                [$type, $entity_id]
            )->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove a share token
     *
     * @param string $type
     * @param int    $entity_id
     * @return void
     * @throws \Exception
     */
    public static function remove($type, $entity_id)
    {
        try {
            static::raw(
                'DELETE FROM `@THIS` WHERE type = ? AND entity_id = ?',
                [$type, $entity_id]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
