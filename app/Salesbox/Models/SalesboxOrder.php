<?php

namespace App\Salesbox\Models;


class SalesboxOrder extends SalesboxModel {
    private $offers;
    private $admin;
    private $user;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->offers = array_map(function($attributes)  {
            return new SalesboxOrderOffer($attributes);
        }, $this->attributes['offers']);
        $this->admin = new SalesboxAdmin($this->attributes['admin']);
        $this->user = new SalesboxUser($this->attributes['user']);
    }

    public function getArchived() {
        return $this->attributes['archived'];
    }

    public function getPlatform() {
        return $this->attributes['platform'];
    }

    public function getCashback() {
        return $this->attributes['cashback'];
    }

    public function getDeliveryPrice() {
        return $this->attributes['deliveryPrice'];
    }

    public function getAssignees() {
        return $this->attributes['assignees'];
    }

    public function getCompanyId() {
        return $this->attributes['companyId'];
    }

    public function getId() {
        return $this->attributes['id'];
    }

    public function getOrderNumber() {
        return $this->attributes['orderNumber'];
    }

    public function getUserId() {
        return $this->attributes['userId'];
    }

    public function getStatus() {
        return $this->attributes['status'];
    }

    public function getAdminId() {
        return $this->attributes['adminId'];
    }

    public function isExecuteNow() {
        return $this->attributes['executeNow'];
    }

    public function getExecuteDate() {
        return $this->attributes['executeDate'];
    }

    public function getComment() {
        return $this->attributes['comment'];
    }

    public function getPromoCodeId() {
        return $this->attributes['promoCodeId'];
    }

    public function getPromoDiscount() {
        return $this->attributes['promoDiscount'];
    }

    public function getTotalPrice() {
        return $this->attributes['totalPrice'];
    }

    public function getPaymentType() {
        return $this->attributes['paymentType'];
    }

    public function getPaymentService() {
        return $this->attributes['paymentService'];
    }

    public function getDeliveryType() {
        return $this->attributes['deliveryType'];
    }

    public function getDeliveryComment() {
        return $this->attributes['deliveryComment'];
    }

    public function getAddressName() {
        return $this->attributes['addressName'];
    }

    public function getAddressLatitude() {
        return $this->attributes['addressLatitude'];
    }

    public function getAddressLongitude() {
        return $this->attributes['addressLongitude'];
    }

    public function getPhone() {
        return $this->attributes['phone'];
    }

    public function getPaymentStatus() {
        return $this->attributes['paymentStatus'];
    }

    public function getBonusesUsed() {
        return $this->attributes['bonusesUsed'];
    }

    public function getWayOfCommunicationId() {
        return $this->attributes['wayOfCommunicationId'];
    }

    public function getPreviewUrl() {
        return $this->attributes['previewUrl'];
    }

    public function getCustomerName() {
        return $this->attributes['customerName'];
    }


    /**
     * @return SalesboxOrderOffer[]
     */
    public function getOffers() {
        return $this->offers;
    }

    public function getUpdatedBy() {
        return $this->attributes['updatedBy'];
    }

    public function getRepayUrl() {
        return $this->attributes['repayUrl'];
    }

    public function getCreatedAt() {
        return $this->attributes['createdAt'];
    }

    public function getUpdatedAt() {
        return $this->attributes['updatedAt'];
    }

    public function get__v() {
        return $this->attributes['__v'];
    }

    public function getUser(): SalesboxUser {
        return $this->user;
    }

    public function getUserComments() {
        return $this->attributes['UserComments'];
    }

    public function getAdmin() {
        return $this->admin;
    }

    public function getPaymentName() {
        return $this->attributes['paymentName'];
    }

    public function getDeliveryName() {
        return $this->attributes['deliveryName'];
    }

    public function getOrderStatusName() {
        return $this->attributes['orderStatusName'];
    }

    public function isCourierDeliveryType(): bool {
        return $this->getDeliveryType() === 'courier';
    }

    public function isPickupDeliveryType(): bool {
        return $this->getDeliveryType() === 'pickup';
    }

    public function isCashPaymentType(): bool {
        return $this->getPaymentType() === 'cash';
    }

    public function isCashlessPaymentType(): bool {
        return $this->getPaymentType() === 'cashless';
    }

    public function isPendingPaymentStatus(): bool {
        return $this->getPaymentStatus() === 'PENDING';
    }

    public function isCanceledPaymentStatus(): bool {
        return $this->getPaymentStatus() === 'CANCELED';
    }
}
