<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Module\DataAdmin\ImportType;
use Gibbon\Domain\System\LogGateway;

// Module Bootstrap
require __DIR__ . '/module.php';

if (isActionAccessible($guid, $connection2, "/modules/Data Admin/import_history_view.php") == false) {
    // Access denied
    $page->addError('You do not have access to this action.');
} else {
    $gibbonLogID = $_GET['gibbonLogID'] ?? 0;

    $logGateway = $container->get(LogGateway::class);
    $importLog = $logGateway->getLogByID($gibbonLogID);

    if (empty($importLog)) {
        $page->addError('There are no records to display.');
        return;
    }

    $importData = isset($importLog['serialisedArray'])? unserialize($importLog['serialisedArray']) : [];
    $importData['log'] = $importLog;
    $importResults = $importData['results'] ?? [];

    if (empty($importData['results']) || !isset($importData['type'])) {
        $page->addError('There are no records to display.');
        return;
    }

    $importType = ImportType::loadImportType($importData['type'], $pdo);
    $importData['name'] = $importType->getDetail('name');

    echo $page->fetchFromTemplate('importer.twig.html', array_merge($importData, $importResults));
}
