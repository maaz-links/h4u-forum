<?php

namespace App\Services;

use App\Models\AdminLog;
use Auth;
use Config;
use Illuminate\Http\Request;

class AdminNav
{
    public static function generateNavs(){
        $admin = Auth::user();

        $admin->load(['roles.permissions']);

        // Get all permission slugs in one array
        $permissionSlugs = [];
        foreach ($admin->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissionSlugs[] = $permission->slug;
            }
        }
        // Remove duplicates if needed
        $permissionSlugs = array_unique($permissionSlugs);

        // dd($permissionSlugs);
        
        $menu = [
            [
                'text' => 'Home',
                'url' => '/home',
                'icon' => '',
            ],
            [
                'text' => 'Admin Password',
                'url' => '/change-password',
                'icon' => '',
            ],
        ];
        if (in_array('manage_chat',$permissionSlugs)) {
            $menu[] = [
                'text' => 'View Chats',
                'url' => '/all-chats',
                'icon' => '',
            ];
        }
        if (in_array('manage_shop',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Shops',
                'url' => '/shops/all',
                'icon' => '',
            ];
            $menu[] = [
                'text' => 'Shops Transaction',
                'url' => '/shop/transactions',
                'icon' => '',
            ];
            $menu[] = [
                'text' => 'Credits Configuration',
                'url' => '/credits-config',
                'icon' => '',
            ];
        }
        if (in_array('view_reports',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Reported Users',
                'url' => '/reports',
                'icon' => '',
            ];
            $menu[] = [
                'text' => 'Reported Chats',
                'url' => '/report-chats',
                'icon' => '',
            ];
        }
        if (in_array('read_contact_requests',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Contact Requests',
                'url' => '/contact-requests',
                'icon' => '',
            ];
        }
        if (in_array('message_moderation',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Message Alerts',
                'url' => '/message-alerts',
                'icon' => '',
            ];
            $menu[] = [
                'text' => ' Message Moderation Rules',
                'url' => '/moderation-rules',
                'icon' => '',
            ];
        }
        if (in_array('configure_reviews',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Reviews Configuration',
                'url' => '/reviews-config',
                'icon' => '',
            ];
        }
        if (in_array('edit_templates',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Pages',
                'icon' => '',
                'submenu' => [
                    [
                        'text' => 'Terms and Conditions',
                        'url' => '/pages/terms-and-conditions',
                        // 'icon' => 'far fa-fw fa-file',
                    ],
                    [
                        'text' => 'Privacy Policy',
                        'url' => '/pages/privacy-policy',
                        // 'icon' => 'far fa-fw fa-file',
                    ],
                    // [
                    //     'text' => 'Credits and Payment',
                    //     'url' => '/pages/credits-and-payment',
                    //     // 'icon' => 'far fa-fw fa-file',
                    // ],
                    [
                        'text' => 'Cookie Policy',
                        'url' => '/pages/cookie-policy',
                        // 'icon' => 'far fa-fw fa-file',
                    ],
                ],
            ];
            $menu[] =  [
                'text' => 'Templates',
                'url' => '/admin/email-templates',
                'icon' => '',
            ];
            $menu[] =  [
                'text' => 'FAQ',
                'url' => '/faqs/all',
                'icon' => '',
            ];

            $menu[] =  [
                'text' => 'Shown Services',
                'url' => '/shown-services',
                'icon' => '',
            ];
            $menu[] =   [
                'text' => 'Web Data',
                'icon' => '',
                'submenu' => [
                    [
                        'text' => 'Hostess Services',
                        'url' => '/webdata/hostess-services',
                    ],
                    [
                        'text' => 'Interests',
                        'url' => '/webdata/interests',
                    ],
                    [
                        'text' => 'Spoken Languages',
                        'url' => '/webdata/spoken-languages',
                    ],
                    [
                        'text' => 'Countries',
                        'url' => '/webdata/europe-countries',
                    ],
                    [
                        'text' => 'Provinces',
                        'url' => '/webdata/europe-provinces',
                    ],
                    [
                        'text' => 'Nationalities',
                        'url' => '/webdata/form-nationalities',
                    ],
                    [
                        'text' => 'Eye Colors',
                        'url' => '/webdata/form-eye-colors',
                    ],
                   
                ],
            ];
        
        }

        if (in_array('generate_profiles',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Profile Scripts',
                'url' => '/profile-scripts',
                'icon' => '',
            ];
        }

        if (in_array('change_backend_settings',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Change Mailer Settings',
                'url' => '/mail-config',
                'icon' => '',
            ];
            $menu[] = [
                'text' => 'Change Payment Settings',
                'url' => '/payment-config',
                'icon' => '',
            ];
        }

        if (in_array('manage_admins',$permissionSlugs)) {
            $menu[] = [
                'text' => 'Admins List',
                'url' => '/admin/users',
                'icon' => '',
            ];
        }
        $menu[] = [
            'text' => 'Admin Logs',
            'url' => '/admin-logs',
            'icon' => '',
        ];

        Config::set('adminlte.menu',$menu);
    }

}