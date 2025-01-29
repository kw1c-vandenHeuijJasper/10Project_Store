@php
    use App\Filament\Customer\Resources\OrderResource;
@endphp
<x-filament-widgets::widget>
    <x-filament::section>

        @php
            $user = auth()->user();
            $phone_missing = $user?->phone_number == null;
            $dob_missing = $user?->date_of_birth == null;

            $both_missing = $phone_missing && $dob_missing;
        @endphp

        @if ($phone_missing || $dob_missing)
            Please edit your profile because: <br />
            @if ($dob_missing)
                Your date of birth is missing. <br />
            @endif
            @if ($both_missing)
                And your
            @endif
            @if ($phone_missing)
                @if (!$both_missing)
                    Your
                @endif
                phone number is missing. <br /> <br />
            @endif
        @endif

        <x-filament::button :href="route('filament.customer.pages.edit-profile')" tag="a" color="info" icon="heroicon-s-user" outlined>
            Edit Profile
        </x-filament::button> <br /> <br />

        @if ($user->hasShoppingCart)
            @php
                $url = OrderResource::getUrl('edit', [
                    'record' => Auth::user()->shoppingCart->id,
                ]);
            @endphp
            <x-filament::button href="{{ $url }}" tag="a" color="success" icon="heroicon-s-shopping-cart"
                outlined>
                Go to shopping cart
            </x-filament::button>
        @elseif($user->canCreateOrder)
            @php
                $url = OrderResource::getUrl('create');
            @endphp

            <x-filament::button href="{{ $url }}" tag="a" color="success"
                icon="heroicon-s-clipboard-document-list" outlined>
                Make an order
            </x-filament::button>
        @endif


    </x-filament::section>
</x-filament-widgets::widget>
