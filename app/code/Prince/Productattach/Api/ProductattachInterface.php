<?php

/**
 * MagePrince
 * Copyright (C) 2018 Mageprince
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category MagePrince
 * @package Prince_Productattach
 * @copyright Copyright (c) 2018 MagePrince
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince
 */

namespace Prince\Productattach\Api;

interface ProductattachInterface
{
    /**
     * Update / insert attachment
     * @param \Prince\Productattach\Api\Data\ProductattachTableInterface $productattachTable
     * @param string $filename
     * @param string $fileContent
     * @return int
     */
    public function UpdateInsertAttachment(
        \Prince\Productattach\Api\Data\ProductattachTableInterface $productattachTable,
        $filename,
        $fileContent
    );

    /**
     * Delete the attachment
     * @param int $int
     * @throws NotFoundException
     * @throws \Exception
     * @return bool
     */
    public function DeleteAttachment(
        $int
    );

    /**
     * Get attachment
     * @param int $int
     * @throws NotFoundException
     * @throws \Exception
     * @return \Prince\Productattach\Api\Data\ProductattachTableInterface
     */
    public function GetAttachment(
        $int
    );

}