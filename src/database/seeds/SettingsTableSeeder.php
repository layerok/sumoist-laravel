<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $settings = [
        [
            'key'                       =>  'site_name',
            'value'                     =>  'E-Commerce Application',
        ],
        [
            'key'                       =>  'site_title',
            'value'                     =>  'E-Commerce',
        ],
        [
            'key'                       =>  'default_email_address',
            'value'                     =>  'admin@admin.com',
        ],
        [
            'key'                       =>  'phone',
            'value'                     =>  '+38 (068) 303 45 51; +38 (068) 303 45 51',
        ],
        [
            'key'                       =>  'start_working',
            'value'                     =>  '11:00',
        ],
        [
            'key'                       =>  'finish_working',
            'value'                     =>  '22:00',
        ],
        [
            'key'                       =>  'currency_code',
            'value'                     =>  'UAN',
        ],
        [
            'key'                       =>  'currency_symbol',
            'value'                     =>  '₴',
        ],
        [
            'key'                       =>  'site_logo',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'site_favicon',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'footer_copyright_text',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'seo_meta_title',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'seo_meta_description',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_facebook',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_youtube',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_whatsapp',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_viber',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_telegram',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_twitter',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'social_instagram',
            'value'                     =>  'emoji_bar_',
        ],
        [
            'key'                       =>  'social_linkedin',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'google_analytics',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'gtm_head',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'gtm_body',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'facebook_pixels',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'wayforpay_test',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'wayforpay_domain',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'wayforpay_account',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'wayforpay_secret_key',
            'value'                     =>  '',
        ],
        [
            'key'                       =>  'theme',
            'value'                     =>  'default',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->settings as $index => $setting)
        {
            $result = Setting::create($setting);
            if (!$result) {
                $this->command->info("Insert failed at record $index.");
                return;
            }
        }
        $this->command->info('Inserted '.count($this->settings). ' records');
    }

    /**
     * @param $key
     */
    public static function get($key)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->first();
        if (!$entry) {
            return;
        }
        return $entry->value;
    }

    /**
     * @param $key
     * @param null $value
     * @return bool
     */
    public static function set($key, $value = null)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->firstOrFail();
        $entry->value = $value;
        $entry->saveOrFail();
        Config::set('key', $value);
        if (Config::get($key) == $value) {
            return true;
        }
        return false;
    }


}
