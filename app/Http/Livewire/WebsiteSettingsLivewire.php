<?php

namespace App\Http\Livewire;

use Exception;
use LVR\Colour\Hex;

class WebsiteSettingsLivewire extends BaseLivewireComponent
{

    // App settings
    public $websiteHeaderTitle;
    public $websiteHeaderSubtitle;
    public $websiteHeaderImage;
    public $oldWebsiteHeaderImage;
    public $websiteFooterImage;
    public $websiteFooterBrief;
    public $oldWebsiteFooterImage;
    public $websiteIntroImage;
    public $oldWebsiteIntroImage;
    //social links
    public $fbLink;
    public $igLink;
    public $twLink;

    public function mount()
    {
        //
        $this->websiteHeaderTitle = setting('websiteHeaderTitle', '');
        $this->websiteHeaderSubtitle = setting('websiteHeaderSubtitle', '');
        $this->websiteFooterBrief = setting('websiteFooterBrief', '');
        $this->oldWebsiteHeaderImage = setting('websiteHeaderImage');
        $this->oldWebsiteFooterImage = setting('websiteFooterImage');
        $this->oldWebsiteIntroImage = setting('websiteIntroImage');

        $this->fbLink = setting('social.fbLink', '');
        $this->igLink = setting('social.igLink', '');
        $this->twLink = setting('social.twLink', '');
    }

    public function render()
    {
        $this->mount();
        return view('livewire.settings.website-settings');
    }


    public function saveAppSettings()
    {



        try {

            $this->isDemo();

            // store new logo
            if ($this->websiteHeaderImage) {
                $this->oldWebsiteHeaderImage = \Storage::url($this->websiteHeaderImage->store('public/website'));
            }
            //store new footer
            if ($this->websiteFooterImage) {
                $this->oldWebsiteFooterImage = \Storage::url($this->websiteFooterImage->store('public/website'));
            }
            //store new intro image
            if ($this->websiteIntroImage) {
                $this->oldWebsiteIntroImage = \Storage::url($this->websiteIntroImage->store('public/website'));
            }

            $websiteSettings = [
                'websiteHeaderTitle' =>  $this->websiteHeaderTitle,
                'websiteHeaderSubtitle' =>  $this->websiteHeaderSubtitle,
                'websiteHeaderImage' =>  $this->oldWebsiteHeaderImage,
                'websiteFooterBrief' =>  $this->websiteFooterBrief,
                'websiteFooterImage' =>  $this->oldWebsiteFooterImage,
                'websiteIntroImage' =>  $this->oldWebsiteIntroImage,
                "social" => [
                    'fbLink' =>  $this->fbLink,
                    'igLink' =>  $this->igLink,
                    'twLink' =>  $this->twLink,
                ]
            ];


            // update the site name
            setting($websiteSettings)->save();



            $this->showSuccessAlert(__("Website Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Website Settings save failed!"));
        }
    }
}
