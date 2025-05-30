<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            'view-role',
            'create-role',
            'edit-role',
            'delete-role',
            'view-permission',
            'create-permission',
            'edit-permission',
            'delete-permission',
            'view-job-requisition',
            'create-job-requisition',
            'edit-job-requisition',
            'delete-job-requisition',
            'view-mass-emailer',
            'create-mass-emailer',
            'edit-mass-emailer',
            'delete-mass-emailer',
            'view-client-database',
            'create-client-database',
            'edit-client-database',
            'delete-client-database',
            'view-social-media-scheduler',
            'create-social-media-scheduler',
            'edit-social-media-scheduler',
            'delete-social-media-scheduler',
            'view-event-generation',
            'create-event-generation',
            'edit-event-generation',
            'delete-event-generation',
            'view-shipping-status',
            'create-shipping-status',
            'edit-shipping-status',
            'delete-shipping-status',
            'view-account-receivable',
            'create-account-receivable',
            'edit-account-receivable',
            'delete-account-receivable',
            'view-human-resource',
            'create-human-resource',
            'edit-human-resource',
            'delete-human-resource',
            'view-procurement',
            'create-procurement',
            'edit-procurement',
            'delete-procurement',
            'view-create-sales-quotation',
            'create-create-sales-quotation',
            'edit-create-sales-quotation',
            'delete-create-sales-quotation',
            'view-download-purchase-order',
            'create-download-purchase-order',
            'edit-download-purchase-order',
            'delete-download-purchase-order',
            'view-order-status',
            'create-order-status',
            'edit-order-status',
            'delete-order-status',
            'view-invoices',
            'create-invoices',
            'edit-invoices',
            'delete-invoices',
            'view-receive-job-order',
            'create-receive-job-order',
            'edit-receive-job-order',
            'delete-receive-job-order',
            'view-internal-job-planing-form',
            'create-internal-job-planing-form',
            'edit-internal-job-planing-form',
            'delete-internal-job-planing-form',
            'view-schedule-job',
            'create-schedule-job',
            'edit-schedule-job',
            'delete-schedule-job',
            'view-create-sales-quotation-system-project',
            'create-create-sales-quotation-system-project',
            'edit-create-sales-quotation-system-project',
            'delete-create-sales-quotation-system-project',
            'view-receive-job-order-system-project',
            'create-receive-job-order-system-project',
            'edit-receive-job-order-system-project',
            'delete-receive-job-order-system-project',
            'view-internal-job-planing-form-system-project',
            'create-internal-job-planing-form-system-project',
            'edit-internal-job-planing-form-system-project',
            'delete-internal-job-planing-form-system-project',
            'view-schedule-job-system-project',
            'create-schedule-job-system-project',
            'edit-schedule-job-system-project',
            'delete-schedule-job-system-project',
            'view-invoicing-status',
            'create-invoicing-status',
            'edit-invoicing-status',
            'delete-invoicing-status',
            'view-payment-status',
            'create-payment-status',
            'edit-payment-status',
            'delete-payment-status',

            'view-create-sales-quotation-it',
            'create-create-sales-quotation-it',
            'edit-create-sales-quotation-it',
            'delete-create-sales-quotation-it',
            'view-receive-job-order-it',
            'create-receive-job-order-it',
            'edit-receive-job-order-it',
            'delete-receive-job-order-it',
            'view-internal-job-planing-form-it',
            'create-internal-job-planing-form-it',
            'edit-internal-job-planing-form-it',
            'delete-internal-job-planing-form-it',
            'view-schedule-job-it',
            'create-schedule-job-it',
            'edit-schedule-job-it',
            'delete-schedule-job-it',
            'view-invoicing-status-it',
            'create-invoicing-status-it',
            'edit-invoicing-status-it',
            'delete-invoicing-status-it',
            'view-payment-status-it',
            'create-payment-status-it',
            'edit-payment-status-it',
            'delete-payment-status-it',
        ];

        // Looping and Inserting Array's Permissions into Permission Table
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
