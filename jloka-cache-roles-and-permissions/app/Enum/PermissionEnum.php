<?php

namespace App\Enum;

enum PermissionEnum: string
{
    case USER_VIEW = 'users.view';
    case USER_CREATE = 'users.create';
    case USER_EDIT = 'users.edit';
    case USER_DELETE = 'users.delete';

    case PRODUCT_VIEW = 'products.view';
    case PRODUCT_CREATE = 'products.create';
    case PRODUCT_EDIT = 'products.edit';
    case PRODUCT_DELETE = 'products.delete';

    case ORDER_VIEW = 'orders.view';
    case ORDER_UPDATE = 'orders.update';
}