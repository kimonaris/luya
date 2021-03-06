<?php
use \admin\Module;
use \yii\helpers\Url;

$user = Yii::$app->adminuser->getIdentity();

$this->beginPage()
?><!DOCTYPE html>
<html ng-app="zaa" ng-controller="LayoutMenuController">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= Yii::$app->siteTitle; ?> // {{currentItem.alias}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= Url::base(true); ?>/admin" />
    <style type="text/css">
        [ng:cloak],
        [ng-cloak],
        [data-ng-cloak],
        [x-ng-cloak],
        .ng-cloak,
        .x-ng-cloak {
            display: none !important;
        }
    </style>
    <?php $this->head() ?>
    <script>
        var authToken = '<?=$user->getAuthToken();?>';
    </script>
</head>
<body ng-cloak>
<?php $this->beginBody() ?>
<!-- ANGULAR SCRIPTS -->

<script type="text/ng-template" id="modal">
    <div class="modal__wrapper" ng-show="!isModalHidden">
        <div class="modal">
            <button class="btn waves-effect waves-light modal__close btn-floating red" ng-click="isModalHidden = true">
                <i class="material-icons">close</i>
            </button>
            <div class="modal-content" ng-transclude></div>
        </div>
        <div class="modal__background"></div>
    </div>
</script>

<script type="text/ng-template" id="storageFileUpload">
    <div class="fileupload">
        <div class="fileupload__btn btn-flat [ grey lighten-4 ]" ng-click="toggleModal()">
            <i class="material-icons left">attach_file</i>
                    <span>
                        Datei auswählen
                    </span>
        </div>
        <span class="fileupload__path" ng-bind="fileinfo.name"></span>

        <modal is-modal-hidden="modal"><storage-file-manager selection="true" /></modal>
    </div>
</script>

<script type="text/ng-template" id="storageImageUpload">
    <div class="imageupload">
        <storage-file-upload ng-model="fileId"></storage-file-upload>

        <div ng-show="originalFileIsRemoved">
            <div class="alert alert--danger">Die Originale Datei wurde entfernt. Sie können keine Filter anwenden ohne original Datei. Laden Sie eine neue Datei hoch und Filter anzuwenden.</div>
        </div><!--
        --><div class="imageupload__preview">
            <img ng-src="{{imageinfo.source}}" class="responsive-img" />
            <div class="imageupload__loading" ng-hide="!imageLoading">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer spinner-green-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="imageupload__filter" ng-show="!noFilters()">
            <label>Filter Auswahl</label>
            <select name="filterId" ng-model="filterId"><option value="0">Kein Filter</option><option ng-repeat="item in filtersData" value="{{ item.id }}">{{ item.name }} ({{ item.identifier }})</option></select>
        </div>
        
    </div>
</script>

<script type="text/ng-template" id="reverseFolders">

    <div class="filemanager__folder-button" ng-click="changeCurrentFolderId(folder.id)">
        <i class="material-icons filemanager__folder-icon filemanager__folder-icon--default">folder_open</i>
        <i class="material-icons filemanager__folder-icon filemanager__folder-icon--active">folder</i>

                        <span class="filemanager__folder-name">
                            {{folder.name }}                                            
                        </span>

        <i class="material-icons filemanager__edit-icon" ng-show="currentFolderId==folder.id" ng-click="toggleFolderMode('edit')">mode_edit</i>
        <i class="material-icons filemanager__delete-icon" ng-show="currentFolderId==folder.id" ng-click="toggleFolderMode('remove')">delete</i>
                        
                        <span ng-show="folderUpdateForm && currentFolderId==folder.id">
                            <input type="text" ng-model="folder.name" class="filemanager__file-dialog__input"/>
                            <div class="filemanager__file-dialog">
                                <span>Speichern?</span>
                                <span class="btn-floating white">
                                    <i class="material-icons filemanager__file-dialog__icon" ng-click="updateFolder(folder)">check</i>
                                </span>
                                <span class="btn-floating white">
                                    <i class="material-icons filemanager__file-dialog__icon filemanager__cancel-icon" ng-click="toggleFolderMode(false)">add</i>
                                </span>
                            </div>
                        </span>
                        <i class="material-icons filemanager__file-move-icon" ng-click="moveFilesTo(folder.id)" ng-show="showFoldersToMove && currentFolderId != folder.id">keyboard_return</i>
                        <span ng-show="folderDeleteForm && currentFolderId==folder.id">
                            <div class="filemanager__file-dialog">
                                <span>Verzeichniss löschen?</span>
                                <span class="btn-floating white">
                                    <i class="material-icons filemanager__file-dialog__icon" ng-click="checkEmptyFolder(folder)">check</i>
                                </span>
                                <span class="btn-floating white">
                                    <i class="material-icons filemanager__file-dialog__icon filemanager__cancel-icon" ng-click="toggleFolderMode(false)">add</i>
                                </span>

                            </div>
                        </span>

                        <span ng-show="folderDeleteConfirmForm && currentFolderId==folder.id">
                            <div class="filemanager__file-dialog">
                                <span>Das Verzeichnis ist nicht leer - trotzdem löschen?</span>
                                <span class="btn-floating white">
                                    <i class="material-icons filemanager__file-dialog__icon" ng-click="deleteFolder(folder)">check</i>
                                </span>
                                <span class="btn-floating white">
                                    <i class="material-icons filemanager__file-dialog__icon filemanager__cancel-icon" ng-click="toggleFolderMode(false)">add</i>
                                </span>
                            </div>
                        </span>

        <!-- mdi-mdi-action-highlight-remove -->
    </div>
    <ul class="filemanager__folders" >
        <li class="filemanager__folder"  ng-class="{'filemanager__folder--active' : currentFolderId == folder.id, 'filemanager__folder--has-subfolders': folder.__items.length > 0}" ng-repeat="folder in foldersData | filemanagerdirsfilter:folder.id"  ng-include="'reverseFolders'"></li>
    </ul>
</script>

<!-- FILEMANAGER -->
<script type="text/ng-template" id="storageFileManager">

    <div class="filemanager">

        <!-- TREE -->
        <div class="filemanager__tree">

            <div class="filemanager__toolbar">

                <div class="floating-form left" ng-class="{ 'floating-form--active' : showFolderForm }">
                    <div class="floating-form__form">
                        <input class="floating-form__input" type="text" ng-model="newFolderName" id="foldername" placeholder="Ordner" />
                    </div><!-- PREVENT WHITESPACE
                         --><div class="floating-form__actions">
                        <span class="[ floating-form__button floating-form__button--active ] btn-floating" ng-click="createNewFolder(newFolderName)"><i class="material-icons">check</i></span>
                        <span class="floating-form__button floating-form__button--active-close btn-floating" ng-click="folderFormToggler()"><i class="material-icons">add</i></span>
                    </div><!-- PREVENT WHITESPACE
                         --><span class="floating-form__label" ng-click="folderFormToggler()">Ordner hinzufügen</span>
                </div>

            </div>

            <!-- FOLDER LIST -->
            <ul class="filemanager__folders">
                <li class="filemanager__folder" ng-class="{'filemanager__folder--active' : currentFolderId == 0 }">
                    <div class="filemanager__folder-button folder-root" ng-click="changeCurrentFolderId(0)">
                        <i class="material-icons filemanager__folder-icon filemanager__folder-icon--default">folder_open</i>
                        <i class="material-icons filemanager__folder-icon filemanager__folder-icon--active">folder</i>
                        <span class="filemanager__folder-name">Stammverzeichnis</span>
                    </div>
                    <ul class="filemanager__folders">
                        <li class="filemanager__folder" ng-class="{'filemanager__folder--active' : currentFolderId == folder.id}" ng-repeat="folder in foldersData | filemanagerdirsfilter:0" ng-include="'reverseFolders'"></li>
                    </ul>
                </li>
            </ul>
            <!-- /FOLDER LIST -->

        </div><!--/TREE

                FILES & FOLDERS
             --><div class="filemanager__files">

            <div class="filemanager__toolbar filemanager__toolbar--top">

                <label class="floating-button-label left" ngf-select ngf-multiple="true" ng-model="uploadingfiles">
                            <span class="btn-floating">
                                <i class="material-icons">file_upload</i>
                            </span>
                    <span class="floating-button-label__label">Datei hinzufügen</span>
                </label>

                <button class="btn btn--small right" ng-show="selectedFiles.length > 0" ng-click="removeFiles()"><b>{{selectedFiles.length}}</b> markierte Dateien löschen</button>
                <button class="btn btn--small right" ng-show="selectedFiles.length > 0" ng-click="showFoldersToMove=!showFoldersToMove">Verschieben nach</button>

                <div class="alert alert--danger" ng-show="errorMsg" style="clear:both;">Fehler beim Hochladen der Datei: {{errorMsg}}</div>

                <div class="modal modal--bottom-sheet" ng-class="{ 'modal--active' : uploading && !serverProcessing }">

                    <div class="row">
                        <div class="col s12">
                            <ul class="collection">
                                <li class="collection-item file" ng-repeat="file in uploadingfiles" ng-class="{ 'file--completed' : file.processed }">
                                    <b>{{file.name}}</b>
                                    <div class="file__progress progress">
                                        <div class="determinate" style="width: {{file.progress}}%"></div>
                                    </div>
                                    <i class="file__icon material-icons">check</i>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="filemanager__upload-overlay" ng-show="uploading || serverProcessing">
                    <div class="filemanager__upload-content">
                        <h3 class="filemanager__upload-title" ng-show="serverProcessing">
                            Der Server verarbeitet Ihre Daten. <br />
                            Bitte warten Sie einen Augenblick.
                        </h3>

                        <div class="filemanager__upload-loader preloader-wrapper big active" ng-show="serverProcessing">
                            <div class="spinner-layer spinner-blue">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>

                            <div class="spinner-layer spinner-red">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>

                            <div class="spinner-layer spinner-yellow">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>

                            <div class="spinner-layer spinner-green">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="row">

            <div class="col" ng-class="{'s8' : fileDetail, 's12' : !fileDetail }">
            <table class="filemanager__table hoverable striped">
                <thead>
                <tr>
                    <th class="filemanager__checkox-column" ng-hide="allowSelection == 'true'">
                        <i class="material-icons clickable" ng-click="toggleSelectionAll()">done_all</i>
                    </th>
                    <th></th>
                    <th>Name</th>
                    <th>Typ</th>
                    <th>Erstellungsdatum</th>
                </tr>
                </thead>
                <tbody>

                <!-- FILES -->
                <tr ng-repeat="file in filesData | filemanagerfilesfilter:currentFolderId:onlyImages" class="filemanager__file" ng-class="{ 'clickable selectable' : allowSelection == 'false' }">
                    <td ng-click="toggleSelection(file)" class="filemanager__checkox-column" ng-hide="allowSelection == 'true'">
                        <input type="checkbox" ng-checked="inSelection(file)" id="{{file.id}}" />
                        <label for="checked-status-managed-by-angular-{{file.id}}"></label>
                    </td>
                    <td ng-click="toggleSelection(file)" class="filemanager__icon-column" ng-class="{ 'filemanager__icon-column--thumb' : file.isImage }">
                        <span ng-if="file.isImage"><img class="responsive-img filmanager__thumb" ng-src="{{file.thumbnail.source}}" /></span>
                        <span ng-if="!file.isImage"><i class="material-icons">attach_file</i></span>
                    </td>
                    <td ng-click="toggleSelection(file)">{{file.name}}</td>
                    <td class="filemanager__lighten">{{file.extension}}</td>
                    <td class="filemanager__lighten">{{file.uploadTimestamp * 1000 | date:"dd.MM.yyyy, HH:mm"}} Uhr</td>
                    <td class="filemanager__lighten" ng-click="openFileDetail(file)"><i class="material-icons">zoom_in</i></td>
                </tr>
                <!-- /FILES -->

                </tbody>
            </table>            
            </div>
            <div class="col s4" ng-show="fileDetail">
                <div class="filemanager__detail-wrapper">
                    <table class="filemanager__table striped">
                        <tbody>
                        <tr>
                            <td><i>Dateiname</i></td><td>{{ fileDetail.name }}</td>
                        </tr>
                        <tr>
                            <td><i>Erstellungsdatum</i></td><td>{{fileDetail.uploadTimestamp * 1000 | date:"dd.MM.yyyy, HH:mm"}} Uhr</td>
                        </tr>
                        <tr>
                            <td><i>Datei Typ</i></td><td>{{ fileDetail.extension }}</td>
                        </tr>
                        <tr>
                            <td><i>Grösse</i></td><td>{{ fileDetail.sizeReadable }}</td>
                        </tr>
                        <tr>
                            <td><i>Interne ID</i></td><td> {{ fileDetail.id }}</td>
                        </tr>
                        <tr>
                            <td><i>Download</i></td><td><a ng-href="{{fileDetail.source}}" target="_blank" class="btn btn-floating"><i class="material-icons">cloud_download</i></a></td>
                        </tr>
                        </tbody>
                    </table>
                    <span ng-if="fileDetail.isImage">
                        <img class="responsive-img" ng-src="{{fileDetail.httpSource}}" />
                    </span>
                    </p>
                    <a class="btn btn--small right" ng-click="closeFileDetail()"><i class="material-icons">zoom_out</i></a>
                </div> <!-- detail-wrapper END -->
            </div>
        </div>
        <!-- FILES & FOLDERS -->

            <div class="filemanager__toolbar filemanager__toolbar--bottom">

                <label class="floating-button-label left" ngf-select ngf-multiple="true" ng-model="uploadingfiles">
                            <span class="btn-floating">
                                <i class="material-icons">file_upload</i>
                            </span>
                    <span class="floating-button-label__label">Datei hinzufügen</span>
                </label>

                <button class="btn btn--small right" ng-show="selectedFiles.length > 0" ng-click="removeFiles()"><b>{{selectedFiles.length}}</b> markierte Dateien löschen</button>
                <button class="btn btn--small right" ng-show="selectedFiles.length > 0" ng-click="showFoldersToMove=!showFoldersToMove">Verschieben nach</button>

                <div class="alert alert--danger" ng-show="errorMsg" style="clear:both;">Fehler beim Hochladen der Datei: {{errorMsg}}</div>

                <div class="modal modal--bottom-sheet" ng-class="{ 'modal--active' : uploading && !serverProcessing }">

                    <div class="row">
                        <div class="col s12">
                            <ul class="collection">
                                <li class="collection-item file" ng-repeat="file in uploadingfiles" ng-class="{ 'file--completed' : file.processed }">
                                    <b>{{file.name}}</b>
                                    <div class="file__progress progress">
                                        <div class="determinate" style="width: {{file.progress}}%"></div>
                                    </div>
                                    <i class="file__icon material-icons">check</i>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

    </div>

</script>
<!-- /FILEMANAGER -->

<!-- /ANGULAR SCRIPTS -->

<div class="luya-container ng-cloak">

    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper blue">
                <a style="cursor: pointer;" data-activates="mobile-demo" class="button-collapse" ng-click="mobileOpen = !mobileOpen"><i class="material-icons">menu</i></a>

                <ul class="left hide-on-med-and-down">
                    <li class="navbar__item" ng-repeat="item in items" ng-class="{'navbar__item--active' : isActive(item) }">
                        <a ng-click="click(item);" class="navbar__link"><i class="material-icons left navbar__icon">{{item.icon}}</i>{{item.alias}}</a>
                    </li>
                </ul>

                <ul class="right navbar__right">
                    <li ng-click="reload()" style="cursor: pointer;">
                        <div class="navbar__button">
                            <i class="material-icons">replay</i>
                            <p class="icon__spawn-text">Reload</p>
                        </div>
                    </li>
                    <li ng-mouseenter="showDebugContainer=1" ng-mouseleave="showDebugContainer=0">
                        <div class="navbar__button">
                            <i class="material-icons">developer_board</i>
                            <p class="icon__spawn-text">Version</p>
                        </div>
                    </li>
                    <li ng-mouseenter="showOnlineContainer=1" ng-mouseleave="showOnlineContainer=0">
                        <div class="navbar__button navbar__button__circle">
                            <div class="navbar__button__circle-icon">
                                <p class="navbar__button__circle-usercount">{{notify.length}}</p>
                            </div>
                            <p class="icon__spawn-text">Online User</p>
                        </div>
                    </li>
                    <li>
                        <div class="navbar__button navbar__button--redhighlight">
                            <a href="<?= Yii::$app->urlManager->createUrl(['admin/default/logout']); ?>" class="navbar__button__anchor">
                                <i class="material-icons">exit_to_app</i>
                                <p class="icon__spawn-text">Logout</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="navbar__button">
                            <i class="material-icons left">account_circle</i><strong><?php echo $user->firstname; ?></strong><!-- NO WHITESPACE
                                    --><p class="icon__spawn-text">Profil</p>
                        </div>
                    </li>
                </ul>

                <ul class="side-nav" id="mobile-demo" ng-class="{ 'side-nav--open' : mobileOpen }">
                    <li ng-repeat="item in items" ng-class="{'active' : isActive(item) }">
                        <a ng-click="click(item);" class="navbar__link"><i class="[ material-icons left ] navbar__icon">{{item.icon}}</i>{{item.alias}}</a>
                    </li>
                </ul>

                <div class="navbar__search-wrapper" ng-class="{ 'navbar__search-wrapper--wide' : searchInputOpen }">
                    <div class="input-field navbar__search" ng-class="{ 'navbar__search--open' : searchInputOpen }">
                        <input id="global-search-input" ng-model="searchQuery" type="search" class="navbar__search-input">
                        <label for="global-search-input" class="navbar__search-label" ng-click="openSearchInput()"><i class="material-icons">search</i></label>
                        <i class="material-icons navbar__search-icon" ng-click="closeSearchInput()">close</i>
                    </div>
                </div>
            </div>
        </nav>
    </div> <!-- /navbar-fixed -->

    <div ng-show="showDebugContainer" class="debug-container">
        <table class="bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Wert</th>
            </tr>
            </thead>
            <tr><td>Luya Version</td><td><?= luya\Module::VERSION; ?></td></tr>
            <tr><td>Id:</td><td><?= Yii::$app->id ?></td></tr>
            <tr><td>Site Title:</td><td><?= Yii::$app->siteTitle ?></td></tr>
            <tr><td>Remote Token:</td><td><?= $this->context->colorizeValue(Yii::$app->remoteToken, true); ?></td></tr>
            <tr><td>YII_DEBUG:</td><td><?= $this->context->colorizeValue(YII_DEBUG); ?></td></tr>
            <tr><td>YII_ENV:</td><td><?= YII_ENV; ?></td></tr>
            <tr><td>AssetManager forceCopy</td><td><?= $this->context->colorizeValue(Yii::$app->assetManager->forceCopy); ?></td></tr>
            <tr><td>Transfer Exceptions:</td><td><?= $this->context->colorizeValue(Yii::$app->errorHandler->transferException); ?></td></tr>
            <tr><td>Yii Timezone:</td><td><?= Yii::$app->timeZone; ?></td></tr>
            <tr><td>PHP Timezone:</td><td><?= date_default_timezone_get(); ?></td></tr>
            <tr><td>PHP memory_limit</td><td><?= ini_get('memory_limit'); ?></td></tr>
            <tr><td>PHP max_execution_time</td><td><?= ini_get('max_execution_time'); ?></td></tr>
            <tr><td>PHP post_max_size</td><td><?= ini_get('post_max_size'); ?></td></tr>
            <tr><td>PHP upload_max_filesize</td><td><?= ini_get('upload_max_filesize'); ?></td></tr>
        </table>
    </div>

    <div ng-show="showOnlineContainer" class="useronline__modal">
        <table>
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>E-Mail</th>
                <td></td>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="row in notify" ng-class="{ 'green lighten-3' : row.is_active, 'red lighten-3' : !row.is_active }">
                <td>
                    <i ng-show="row.is_active" class="material-icons">tag_faces</i>
                    <i ng-show="!row.is_active" class="material-icons">help_outline</i>
                </td>
                <td>{{row.firstname}} {{row.lastname}}</td>
                <td>{{row.email}}</td>
                <td>
                    <small ng-hide="row.is_active">
                        <b>Inaktiv seit</b><br />
                        {{row.inactive_since}}
                    </small>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div ng-class="{ 'search-box--open' : searchQuery }" class="search-box" zaa-esc="escapeSearchInput()">

        <div class="center" ng-show="searchResponse==null && searchQuery.length <= 2 && searchQuery.length > 0">
            <br /><br /><br />
            <p>Bitte geben Sie einen Suchbegriff mit mindestens <b>drei Buchstaben</b> ein.</p>
        </div>

        <div class="center" ng-show="(searchResponse.length == 0 && searchResponse != null) && searchQuery.length > 2">
            <br /><br /><br />
            <p>Es wurden keine Ergebnisse gefunden.</p>
        </div>

        <div class="center" ng-show="searchResponse==null && searchQuery.length > 2">
            <br /><br /><br />
            <div class="preloader-wrapper small active">
                <div class="spinner-layer spinner-blue-only">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                        <div class="circle"></div>
                    </div><div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" ng-repeat="item in searchResponse">
            <div class="col s12">
                <b class="search-box__group-title"><i class="left material-icons">{{item.menuItem.icon}}</i> {{item.menuItem.alias}}</b>

                <table>
                    <thead>
                    <tr ng-repeat="row in item.data | limitTo:1">
                        <th ng-repeat="(k,v) in row">{{k}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="row in item.data">
                        <td ng-repeat="(k,v) in row">{{v}}</td>
                        <td style="width: 20px;"><a href="" class="right"><i class="material-icons">chevron_right</i></a></td>
                    </tr>
                    </tbody>
                </table>
                <br /><br />
            </div>
        </div>
    </div>

    <!-- ANGULAR-VIEW -->
    <div class="luya-container__angular-placeholder module-{{currentItem.moduleId}}" ui-view></div>
    <!-- /ANGULAR-VIEW -->

</div> <!-- /.luya-container -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>