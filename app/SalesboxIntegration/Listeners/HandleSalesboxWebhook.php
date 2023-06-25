<?php

namespace App\SalesboxIntegration\Listeners;

use App\Poster\Facades\PosterStore;
use App\Poster\ServiceMode;
use App\Salesbox\Events\SalesboxWebhookReceived;
use App\Salesbox\Models\SalesboxOrderOffer;
use DateTime;
use Illuminate\Http\Request;
use App\Salesbox\Facades\SalesboxStore;
use \poster\src\PosterApi;


class HandleSalesboxWebhook
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(SalesboxWebhookReceived $event)
    {
        $id1 = 'd71876ce-c3e7-4b87-be1e-283fea769ea3';
        $id2 = '416ae62d-a36f-4101-83fc-bfd0b30ce678';

        SalesboxStore::authenticate();
        PosterStore::init();
        $order = SalesboxStore::getOrderById($id1);
        $user = $order->getUser();

        $incomingOrder = [
            'spot_id' => 1,
            'phone' => $order->getPhone(),
            'first_name' => $user->getFirstName() ?: null,
            'last_name' => $user->getLastName() ?: null,
            'email' => $user->getEmail() ?: null,
            'service_mode' => ServiceMode::ON_SITE,
            'comment' => $order->getComment() ?: null
        ];

        if (!$order->isExecuteNow()) {
            $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $order->getExecuteDate());
            $date = date("Y-m-d h:m:s", $datetime->getTimestamp());
            $incomingOrder['delivery_time'] = $date;
        }

        if ($order->isCourierDeliveryType()) {
            $incomingOrder['service_mode'] = ServiceMode::COURIER;
        }

        if ($order->isPickupDeliveryType()) {
            $incomingOrder['service_mode'] = ServiceMode::TAKEAWAY;
        }

        if ($order->getAddressName()) {
            $incomingOrder['client_address'] = [
                'address1' => $order->getAddressName(),
                'address2' => null,
                'comment' => $order->getDeliveryComment(),
                'lat' => $order->getAddressLatitude(),
                'lng' => $order->getAddressLongitude(),
            ];
        }

        $incomingOrder['products'] = collect($order->getOffers())
            ->filter(function(SalesboxOrderOffer $offer) {
                return !!$offer->getExternalId();
            })
            ->map(function (SalesboxOrderOffer $offer) {
                return [
                    'product_id' => $offer->getExternalId(),
                    'count' => $offer->getCount(),
                    'modificator_id' => $offer->getModifierId(),
                ];
            });


        $res = PosterApi::incomingOrders()->createIncomingOrder($incomingOrder);
        if(!isset($res->response)) {
            throw new \RuntimeException(sprintf("Couldn't send salesbox order#%d to poster: ", $order->getOrderNumber()). json_encode((array)$res));
        }

        return true;
    }

}
