<?php

/**
 * Copyright 2011-2013 Stephen Just <stephenjust@users.sf.net>
 *                2014 Daniel Butum <danibutum at gmail dot com>
 * This file is part of stkaddons
 *
 * stkaddons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stkaddons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stkaddons.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Handles management of temporary files
 * @author Stephen
 */
class Cache
{
    /**
     * Empty the cache folder, leave certain files in place
     *
     * @param string $exclude_regex files to exclude regex
     *
     * @throws CacheException
     */
    public static function clear($exclude_regex = '/^^(cache_graph_.*\.json)|\.gitignore$/i')
    {
        try
        {
            File::deleteDir(CACHE_PATH, $exclude_regex);
        }
        catch(FileException $e)
        {
            throw new CacheException($e->getMessage());
        }

        try
        {
            $cache_list = DBConnection::get()->query(
                'SELECT `file`
                 FROM `' . DB_PREFIX . 'cache`',
                DBConnection::FETCH_ALL
            );
            foreach ($cache_list as $cache_item)
            {
                if (preg_match($exclude_regex, $cache_item['file']))
                {
                    continue;
                }

                DBConnection::get()->delete("cache", "`file` = :file", [':file' => $cache_item['file']]);
            }
        }
        catch(DBException $e)
        {
            throw new CacheException(exception_message_db(_("empty the cache")));
        }
    }

    /**
     * Clear the addon cache files
     * This method silently fails
     *
     * @param string $addon the addon id
     *
     * @return bool
     */
    public static function clearAddon($addon)
    {
        $addon = Addon::cleanId($addon);
        if (!Addon::exists($addon))
        {
            return false;
        }

        try
        {
            $cache_list = DBConnection::get()->query(
                'SELECT `file`
                FROM `' . DB_PREFIX . 'cache`
                WHERE `addon` = :addon',
                DBConnection::FETCH_ALL,
                [':addon' => $addon]
            );
            foreach ($cache_list AS $cache_item)
            {
                try
                {
                    File::deleteFileFS(CACHE_PATH . $cache_item['file']);
                }
                catch(FileException $e)
                {
                    Log::newEvent($e->getMessage());

                    return false;
                }

                DBConnection::get()->delete("cache", "`file` = :file", [':file' => $cache_item['file']]);
            }
        }
        catch(DBException $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Add a database record for a cache file
     * This method will silently fail
     *
     * @param string $path  Relative to cache root
     * @param string $addon Associated add-on's id or NULL
     * @param string $props File properties (e.g. w=1,h=3)
     *
     * @return boolean
     */
    public static function createFile($path, $addon = null, $props = null)
    {
        $addon = Addon::exists($addon) ? Addon::cleanId($addon) : null;

        try
        {
            DBConnection::get()->insert(
                "cache",
                [
                    ":file"  => $path,
                    ":addon" => $addon,
                    ":props" => $props
                ]
            );
        }
        catch(DBException $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Check if a cache file exist (based on database record of its existence)
     * This method silently fails
     *
     * @param string $path Relative to upload root
     *
     * @return array Empty array on failure, array with 'path', 'addon' otherwise
     */
    public static function fileExists($path)
    {
        try
        {
            $result = DBConnection::get()->query(
                'SELECT `addon`, `props`
                FROM `' . DB_PREFIX . 'cache`
                WHERE `file` = :file',
                DBConnection::FETCH_ALL,
                [':file' => (string)$path]
            );
        }
        catch(DBException $e)
        {
            return [];
        }

        if (empty($result))
        {
            return [];
        }

        return $result;
    }

    /**
     * Get image properties for a cacheable image
     *
     * @param int $id   the id of the file
     * @param int $size image size, see SImage::SIZE_
     *
     * @return array
     * @throws CacheException
     */
    public static function getImage($id, $size = null)
    {
        // TODO call File class
        try
        {
            $file = DBConnection::get()->query(
                'SELECT `file_path`, `is_approved`
                FROM `' . DB_PREFIX . 'files`
                WHERE `id` = :id
                LIMIT 1',
                DBConnection::FETCH_FIRST,
                [':id' => $id],
                [':id' => DBConnection::PARAM_INT]
            );
        }
        catch(DBException $e)
        {
            throw new CacheException(exception_message_db(_('look up cache image file')));
        }

        // image does with tha id does not exist in the database
        if (!$file)
        {
            return [
                'url'         => IMG_LOCATION . 'notfound.png',
                'is_approved' => true,
                'exists'      => false
            ];
        }

        $return = [
            'url'         => DOWNLOAD_LOCATION . $file['file_path'],
            'is_approved' => (bool)$file['is_approved'],
            'exists'      => true
        ];

        $cache_prefix = $size ? Cache::cachePrefix($size) : "";

        // image exists in cache
        $basename = basename($file['file_path']);
        if (static::fileExists($cache_prefix . $basename))
        {
            $return['url'] = CACHE_LOCATION . $cache_prefix . $basename;
        }
        else // create new cache by resizing the image
        {
            $return['url'] = ROOT_LOCATION . 'image.php?size=' . $size . '&amp;pic=' . $file['file_path'];
        }

        return $return;
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public static function cachePrefix($size)
    {
        if (!$size)
        {
            return '';
        }

        if ($size === SImage::SIZE_BIG)
        {
            return '300--';
        }
        if ($size === SImage::SIZE_MEDIUM)
        {
            return '75--';
        }
        if ($size === SImage::SIZE_SMALL)
        {
            return '25--';
        }

        return '100--';
    }
}
