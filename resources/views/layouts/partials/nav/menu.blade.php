<ul class="mt-6">


    {{-- dashboard --}}
    <x-menu-item title="{{ __('Dashboard') }}" route="dashboard">
        <x-heroicon-o-template class="w-5 h-5" />
    </x-menu-item>

    @role('admin')
    <x-menu-item title="{{ __('Banners') }}" route="banners">
        <x-heroicon-o-photograph class="w-5 h-5" />
    </x-menu-item>
    @endrole

    {{-- Vendors --}}
    <x-group-menu-item routePath="vendors*" title="{{ __('Vendor Mangt.') }}" icon="heroicon-o-cube">

        @role('admin')
        {{-- Vendor Types --}}
        <x-menu-item title="{{ __('Vendor Types') }}" route="vendor.types">
            <x-heroicon-o-color-swatch class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Zones') }}" route="zones">
            <x-heroicon-o-flag class="w-5 h-5" />
        </x-menu-item>

        @endrole

        <x-menu-item title="{{ __('Vendors') }}" route="vendors">
            <x-heroicon-o-shopping-cart class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>


    @role('manager')
    <x-menu-item title="{{ __('Delivery Boys') }}" route="drivers">
        <x-heroicon-o-user-group class="w-5 h-5" />
    </x-menu-item>
    @endhasanyrole

    <x-hr />

    <x-group-menu-item routePath="categories*" title="{{ __('Categories') }}" icon="heroicon-o-bookmark">

        <x-menu-item title="{{ __('Categories') }}" route="categories">
            <x-heroicon-o-folder class="w-5 h-5" />
        </x-menu-item>
        <x-menu-item title="{{ __('SubCategories') }}" route="subcategories">
            <x-heroicon-o-document-duplicate class="w-5 h-5" />
        </x-menu-item>
    </x-group-menu-item>


    {{-- Products --}}
    @showProduct
    <x-group-menu-item routePath="product/*" title="{{ __('Products') }}" icon="heroicon-o-archive">

        <x-menu-item title="{{ __('Products') }}" route="products">
            <x-heroicon-o-archive class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Menus') }}" route="products.menus">
            <x-heroicon-o-book-open class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Option Groups') }}" route="products.options.group">
            <x-heroicon-o-collection class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Options') }}" route="products.options">
            <x-heroicon-o-dots-horizontal class="w-5 h-5" />
        </x-menu-item>
        @role('admin')
        <x-menu-item title="{{ __('Favourites') }}" route="favourites">
            <x-heroicon-o-star class="w-5 h-5" />
        </x-menu-item>
        @endrole
    </x-group-menu-item>
    @endshowProduct

    {{-- Package --}}
    @showPackage
    <x-group-menu-item routePath="package/*" title="{{ __('Package Delivery') }}" icon="heroicon-o-globe">

        @hasanyrole('city-admin|admin')
        <x-menu-item title="{{ __('Package Types') }}" route="package.types">
            <x-heroicon-o-archive class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Countries') }}" route="package.countries">
            <x-heroicon-o-globe class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('States') }}" route="package.states">
            <x-heroicon-o-globe-alt class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Cities') }}" route="package.cities">
            <x-heroicon-o-map class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole

        {{-- manager package delivery options --}}
        @role('manager')
        <x-menu-item title="{{ __('Pricing') }}" route="package.pricing">
            <x-heroicon-o-currency-dollar class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Cities') }}" route="package.cities.my">
            <x-heroicon-o-location-marker class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('States') }}" route="package.states.my">
            <x-heroicon-o-globe-alt class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Countries') }}" route="package.countries.my">
            <x-heroicon-o-globe class="w-5 h-5" />
        </x-menu-item>

        @endhasanyrole

    </x-group-menu-item>

    @endshowPackage

    {{-- Services --}}
    @showService
    <x-group-menu-item routePath="service/*" title="{{ __('Services') }}" icon="heroicon-o-rss">

        <x-menu-item title="{{ __('Services') }}" route="services">
            <x-heroicon-o-rss class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>
    @endshowService

    {{-- taxi booking --}}
    @role('admin')
    <x-group-menu-item routePath="taxi/*" title="{{ __('Taxi Booking') }}" icon="heroicon-o-status-online">

        <x-menu-item title="{{ __('Vehicle Types') }}" route="taxi.vehicle.types">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Vehicles') }}" route="taxi.vehicles">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Car Makes') }}" route="taxi.car.makes">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Car Models') }}" route="taxi.car.models">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

        {{-- Payment methods --}}
        <x-menu-item title="{{ __('Payment Methods') }}" route="taxi.payment.methods">
            <x-heroicon-o-cash class="w-5 h-5" />
        </x-menu-item>

        {{-- Price --}}
        <x-menu-item title="{{ __('Pricing') }}" route="taxi.pricing">
            <x-heroicon-o-cash class="w-5 h-5" />
        </x-menu-item>

        {{-- Taxi settings --}}
        <x-menu-item title="{{ __('Settings') }}" route="taxi.settings">
            <x-heroicon-o-cog class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>
    @endhasanyrole
    <x-hr />
    {{-- orders --}}
    <x-group-menu-item routePath="order/*" title="{{ __('Orders') }}" icon="heroicon-o-shopping-bag">

        <x-menu-item title="{{ __('Orders') }}" route="orders">
            <x-heroicon-o-shopping-bag class="w-5 h-5" />
        </x-menu-item>
        @role('admin')
        <x-menu-item title="{{ __('Reviews') }}" route="reviews">
            <x-heroicon-o-thumb-up class="w-5 h-5" />
        </x-menu-item>
        @endrole
        @hasanyrole('city-admin|admin')
        <x-menu-item title="{{ __('Delivery Address') }}" route="delivery.addresses">
            <x-heroicon-o-location-marker class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole

        <x-menu-item title="{{ __('Coupons') }}" route="coupons">
            <x-heroicon-o-receipt-tax class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>

    @hasanyrole('city-admin|admin')

    @endhasanyrole
    {{-- Users --}}
    @hasanyrole('city-admin|admin')
    <x-menu-item title="{{ __('Users') }}" route="users">
        <x-heroicon-o-user-group class="w-5 h-5" />
    </x-menu-item>
    @endhasanyrole

    @hasanyrole('admin|manager')
    <x-hr />

    {{-- Payments --}}
    <x-group-menu-item routePath="payments/*" title="{{ __('Payments') }}" icon="heroicon-o-cash">
        @hasanyrole('admin')
        {{-- wallet transactions --}}
        <x-menu-item title="{{ __('Wallet Transactions') }}" route="wallet.transactions">
            <x-heroicon-o-collection class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole

        <x-menu-item title="{{ __('Payment Accounts') }}" route="payment.accounts">
            <x-heroicon-o-calculator class="w-5 h-5" />
        </x-menu-item>

        @hasanyrole('manager')
        <x-menu-item title="{{ __('My Payouts') }}" route="my.payouts">
            <x-heroicon-o-collection class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole

    </x-group-menu-item>


    @endhasanyrole

    {{-- Earings --}}
    <x-group-menu-item routePath="earnings/*" title="{{ __('Earnings') }}" icon="heroicon-o-cash">
        @hasanyrole('city-admin|admin')
        <x-menu-item title="{{ __('Vendor Earnings') }}" route="earnings.vendors">
            <x-heroicon-o-shopping-bag class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole

        <x-menu-item title="{{ __('Driver Earnings') }}" route="earnings.drivers">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

        <x-menu-item title="{{ __('Driver Remittance') }}" route="earnings.remittance">
            <x-heroicon-o-calculator class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>

    {{-- Payouts --}}
    <x-group-menu-item routePath="payouts*" title="{{ __('Payouts') }}" icon="heroicon-o-clipboard-check">
        @hasanyrole('city-admin|admin')
        <x-menu-item title="{{ __('Vendor Payouts') }}" route="payouts" rawRoute="{{ route('payouts', ['type' => 'vendors']) }}">
            <x-heroicon-o-shopping-bag class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole
        <x-menu-item title="{{ __('Driver Payouts') }}" route="payouts" rawRoute="{{ route('payouts', ['type' => 'drivers']) }}">
            <x-heroicon-o-truck class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>


    <x-group-menu-item routePath="subscription*" title="{{ __('Subscription') }}" icon="heroicon-o-shield-check">
        {{-- subscription list --}}
        @hasanyrole('admin')
        <x-menu-item title="{{ __('Subscriptions') }}" route="subscriptions">
            <x-heroicon-o-ticket class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole
        {{-- vendors and current subscriptions --}}
        @hasanyrole('city-admin|admin')
        <x-menu-item title="{{ __('Vendor Subscriptions') }}" route="vendors.subscriptions">
            <x-heroicon-o-bookmark class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole
        {{-- vendor subscription history --}}
        @hasanyrole('manager')
        <x-menu-item title="{{ __('My Subscriptions') }}" route="my.subscriptions">
            <x-heroicon-o-bookmark class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole

    </x-group-menu-item>

    {{-- Payment methods --}}
    @hasanyrole('manager')
    <x-menu-item title="{{ __('Payment Methods') }}" route="payment.methods.my">
        <x-heroicon-o-cash class="w-5 h-5" />
    </x-menu-item>
    @endhasanyrole


    @hasanyrole('admin')
    <x-hr />
    <x-group-menu-item routePath="operations/*" title="{{ __('Operations') }}" icon="heroicon-o-server">

        {{-- notifications --}}
        <x-menu-item title="{{ __('Notifications') }}" route="notification.send">
            <x-heroicon-o-bell class="w-5 h-5" />
        </x-menu-item>

        {{-- backups --}}
        <x-menu-item title="{{ __('Backup') }}" route="backups">
            <x-heroicon-o-database class="w-5 h-5" />
        </x-menu-item>

        {{-- import --}}
        <x-menu-item title="{{ __('Import') }}" route="imports">
            <x-heroicon-o-cloud-upload class="w-5 h-5" />
        </x-menu-item>

        {{-- logs --}}
        <x-menu-item title="{{ __('Logs') }}" route="logs" ex="true">
            <x-heroicon-o-shield-exclamation class="w-5 h-5" />
        </x-menu-item>

        {{-- data reset --}}
        <x-menu-item title="{{ __('Clear Data') }}" route="data.clear">
            <x-heroicon-o-backspace class="w-5 h-5" />
        </x-menu-item>

        {{-- cron job --}}
        <x-menu-item title="{{ __('CRON JOB') }}" route="configure.cron.job">
            <x-heroicon-o-calendar class="w-5 h-5" />
        </x-menu-item>
        {{-- cron job --}}
        <x-menu-item title="{{ __('Auto-Assignments') }}" route="auto.assignments">
            <x-heroicon-o-clipboard-check class="w-5 h-5" />
        </x-menu-item>

        {{-- troubleshoot --}}
        <x-menu-item title="{{ __('Troubleshoot') }}" route="troubleshooting">
            <x-heroicon-o-light-bulb class="w-5 h-5" />
        </x-menu-item>

    </x-group-menu-item>


    {{-- Settings --}}
    <x-group-menu-item routePath="setting/*" title="{{ __('Settings') }}" icon="heroicon-o-cog">

        {{-- Currencies --}}
        <x-menu-item title="{{ __('Currencies') }}" route="currencies">
            <x-heroicon-o-currency-dollar class="w-5 h-5" />
        </x-menu-item>

        {{-- Payment methods --}}
        <x-menu-item title="{{ __('Payment Methods') }}" route="payment.methods">
            <x-heroicon-o-cash class="w-5 h-5" />
        </x-menu-item>

        {{-- Settings --}}
        <x-menu-item title="{{ __('SMS Gateways') }}" route="sms.settings">
            <x-heroicon-o-inbox class="w-5 h-5" />
        </x-menu-item>

        <x-hr />


        {{-- Settings --}}
        <x-menu-item title="{{ __('General Settings') }}" route="settings">
            <x-heroicon-o-cog class="w-5 h-5" />
        </x-menu-item>

        {{-- App Settings --}}
        <x-menu-item title="{{ __('Mobile App Settings') }}" route="settings.app">
            <x-heroicon-o-device-mobile class="w-5 h-5" />
        </x-menu-item>

        {{-- Web Settings --}}
        <x-menu-item title="{{ __('Website Settings') }}" route="settings.website">
            <x-heroicon-o-globe-alt class="w-5 h-5" />
        </x-menu-item>

        {{-- Mail Settings --}}
        <x-menu-item title="{{ __('Server Settings') }}" route="settings.server">
            <x-heroicon-o-server class="w-5 h-5" />
        </x-menu-item>

        {{-- translation --}}
        @production
        @else
        <x-menu-item title="{{ __('Translation') }}" route="translation">
            <x-heroicon-o-translate class="w-5 h-5" />
        </x-menu-item>
        @endproduction

        {{-- upgrade --}}
        <x-menu-item title="{{ __('Upgrade') }}" route="upgrade">
            <x-heroicon-o-cloud-upload class="w-5 h-5" />
        </x-menu-item>
    </x-group-menu-item>
    @endhasanyrole

    {{-- extensions --}}
    @hasanyrole('admin|city-admin')
    <x-menu-item title="{{ __('Extensions') }}" route="extensions">
        <x-heroicon-o-puzzle class="w-5 h-5" />
    </x-menu-item>
    @endhasanyrole


    <x-hr />
    {{-- reports --}}
    <x-group-menu-item routePath="reports/*" title="{{ __('Reports') }}" icon="heroicon-o-chart-square-bar">

        @hasanyrole('admin|city-admin')
        <x-menu-item title="{{ __('Coupon Report') }}" route="reports.coupons">
            <x-heroicon-o-chart-pie class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole
        {{-- products --}}
        @showProduct
        <x-menu-item title="{{ __('Products') }}" route="reports.products">
            <x-heroicon-o-archive class="w-5 h-5" />
        </x-menu-item>
        @endshowProduct
        {{-- services --}}
        @showService
        <x-menu-item title="{{ __('Services') }}" route="reports.services">
            <x-heroicon-o-rss class="w-5 h-5" />
        </x-menu-item>
        @endshowService
        @hasanyrole('admin|city-admin')
        <x-menu-item title="{{ __('Vendors') }}" route="reports.vendors">
            <x-heroicon-o-shopping-cart class="w-5 h-5" />
        </x-menu-item>
        <x-menu-item title="{{ __('Customers') }}" route="reports.customers">
            <x-heroicon-o-users class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole
        @hasanyrole('admin')
        <x-menu-item title="{{ __('Subscriptions') }}" route="reports.subscriptions">
            <x-heroicon-o-bookmark class="w-5 h-5" />
        </x-menu-item>
        @endhasanyrole
    </x-group-menu-item>



</ul>
