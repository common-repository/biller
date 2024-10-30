<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6bf26a6ea6a32586e6ae749b5b6163e8
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Biller\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Biller\\' => 
        array (
            0 => __DIR__ . '/..' . '/biller/integration-core/src',
        ),
    );

    public static $classMap = array (
        'Biller\\Biller' => __DIR__ . '/../..' . '/includes/class-biller.php',
        'Biller\\Components\\Bootstrap_Component' => __DIR__ . '/../..' . '/includes/Components/class-bootstrap-component.php',
        'Biller\\Components\\Exceptions\\Biller_Cancellation_Rejected_Exception' => __DIR__ . '/../..' . '/includes/Components/Exeptions/class-biller-cancellation-rejected-exception.php',
        'Biller\\Components\\Exceptions\\Biller_Capture_Rejected_Exception' => __DIR__ . '/../..' . '/includes/Components/Exeptions/class-biller-capture-rejected-exception.php',
        'Biller\\Components\\Services\\Admin_Order_Action_Handlers' => __DIR__ . '/../..' . '/includes/Components/Services/class-admin-order-action-handlers.php',
        'Biller\\Components\\Services\\Cancellation_Service' => __DIR__ . '/../..' . '/includes/Components/Services/Core/class-cancellation-service.php',
        'Biller\\Components\\Services\\Configuration_Service' => __DIR__ . '/../..' . '/includes/Components/Services/class-configuration-service.php',
        'Biller\\Components\\Services\\Logger_Service' => __DIR__ . '/../..' . '/includes/Components/Services/class-logger-service.php',
        'Biller\\Components\\Services\\Notice_Service' => __DIR__ . '/../..' . '/includes/Components/Services/class-notice-service.php',
        'Biller\\Components\\Services\\Null_Channel_Adapter_Service' => __DIR__ . '/../..' . '/includes/Components/Services/Core/class-null-channel-adapter-service.php',
        'Biller\\Components\\Services\\Order_Refund_Service' => __DIR__ . '/../..' . '/includes/Components/Services/Core/class-order-refund-service.php',
        'Biller\\Components\\Services\\Order_Request_Service' => __DIR__ . '/../..' . '/includes/Components/Services/class-order-request-service.php',
        'Biller\\Components\\Services\\Order_Status_Transition_Service' => __DIR__ . '/../..' . '/includes/Components/Services/Core/class-order-status-transition-service.php',
        'Biller\\Components\\Services\\Refund_Amount_Service' => __DIR__ . '/../..' . '/includes/Components/Services/class-refund-amount-service.php',
        'Biller\\Components\\Services\\Shipment_Service' => __DIR__ . '/../..' . '/includes/Components/Services/Core/class-shipment-service.php',
        'Biller\\Controllers\\Biller_Base_Controller' => __DIR__ . '/../..' . '/includes/Controllers/class-biller-base-controller.php',
        'Biller\\Controllers\\Biller_Notifications_Controller' => __DIR__ . '/../..' . '/includes/Controllers/class-biller-notifications-controller.php',
        'Biller\\Controllers\\Biller_Order_Cancel_Controller' => __DIR__ . '/../..' . '/includes/Controllers/class-biller-order-cancel-controller.php',
        'Biller\\Controllers\\Biller_Order_Capture_Controller' => __DIR__ . '/../..' . '/includes/Controllers/class-biller-order-capture-controller.php',
        'Biller\\Controllers\\Biller_Order_Details_Controller' => __DIR__ . '/../..' . '/includes/Controllers/class-biller-order-details-controller.php',
        'Biller\\Controllers\\Biller_Payment_Redirection_Controller' => __DIR__ . '/../..' . '/includes/Controllers/class-biller-payment-redirection-controller.php',
        'Biller\\DTO\\Notice' => __DIR__ . '/../..' . '/includes/DTO/Notice.php',
        'Biller\\Gateways\\Biller_Business_Invoice' => __DIR__ . '/../..' . '/includes/Gateways/class-biller-business-invoice.php',
        'Biller\\Migrations\\Abstract_Migration' => __DIR__ . '/../..' . '/includes/Migrations/class-abstract-migration.php',
        'Biller\\Migrations\\Exceptions\\Migration_Exception' => __DIR__ . '/../..' . '/includes/Migrations/Exceptions/class-migration-exception.php',
        'Biller\\Migrations\\Migrator' => __DIR__ . '/../..' . '/includes/Migrations/class-migrator.php',
        'Biller\\Migrations\\Schema\\Biller_Entity_Schema_Provider' => __DIR__ . '/../..' . '/includes/Migrations/Schema/class-biller-entity-schema-provider.php',
        'Biller\\Migrations\\Scripts\\Migration_1_0_0' => __DIR__ . '/../..' . '/includes/Migrations/Scripts/migration.v.1.0.0.php',
        'Biller\\Migrations\\Utility\\Migration_Reader' => __DIR__ . '/../..' . '/includes/Migrations/Utility/class-migration-reader.php',
        'Biller\\Repositories\\Base_Repository' => __DIR__ . '/../..' . '/includes/Repositories/class-base-repository.php',
        'Biller\\Repositories\\Plugin_Options_Repository' => __DIR__ . '/../..' . '/includes/Repositories/class-plugin-options-repository.php',
        'Biller\\Utility\\Database' => __DIR__ . '/../..' . '/includes/Utility/class-database.php',
        'Biller\\Utility\\Logging_Callable' => __DIR__ . '/../..' . '/includes/Utility/class-logging-callable.php',
        'Biller\\Utility\\Script_Loader' => __DIR__ . '/../..' . '/includes/Utility/class-script-loader.php',
        'Biller\\Utility\\Shop_Helper' => __DIR__ . '/../..' . '/includes/Utility/class-shop-helper.php',
        'Biller\\Utility\\Status_Mapper' => __DIR__ . '/../..' . '/includes/Utility/class-status-mapper.php',
        'Biller\\Utility\\Translator' => __DIR__ . '/../..' . '/includes/Utility/class-translator.php',
        'Biller\\Utility\\View' => __DIR__ . '/../..' . '/includes/Utility/class-view.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6bf26a6ea6a32586e6ae749b5b6163e8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6bf26a6ea6a32586e6ae749b5b6163e8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6bf26a6ea6a32586e6ae749b5b6163e8::$classMap;

        }, null, ClassLoader::class);
    }
}
