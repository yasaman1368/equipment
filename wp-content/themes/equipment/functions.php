
<?php
include_once '_inc/register-assets/register-assets.php';
include_once '_inc/utility/locations.php';
include_once '_inc/autoLoader/AutoLoader.php';
include_once '_inc/form-equipment/save-form-data.php';
include_once '_inc/form-equipment/show-edite-equiment-forms.php';
include_once '_inc/form-equipment/get-saved-forms.php';
include_once '_inc/form-equipment/get-form-fields.php';
include_once '_inc/form-equipment/save-equipment-data.php';
include_once '_inc/form-equipment/get-equipment-data.php';
include_once '_inc/form-equipment/get-form-and-fields.php';
include_once '_inc/form-equipment/remove-equipment-data.php';
include_once '_inc/form-equipment/remove-form.php';
include_once '_inc/export/export-equipments.php';
include_once '_inc/users/search-users/handler-user-search.php';
include_once '_inc/users/add-new-user/add-new-user.php';
include_once '_inc/users/get-user-list/get-user-list.php';
include_once '_inc/users/remove-user/remove-user.php';
include_once '_inc/users/get-user-details/get_user_details.php';
include_once '_inc/users/update-user/update-user.php';
include_once '_inc/users/user-login/handle-user-login.php';
include_once '_inc/fetch-student-report/fetch-student-report.php';
include_once '_inc/users/add-location/CRUD-location.php';
include_once '_inc/utility/get-user-role.php';
include_once 'panel/router.php';

define('COMPOSER_ROOT', get_template_directory() . '/composer');
include_once COMPOSER_ROOT . '/vendor/autoload.php';
include_once '_inc/create-excel-file/create-excel-file.php';
