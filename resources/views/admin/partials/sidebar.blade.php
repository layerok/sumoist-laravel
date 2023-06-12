
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <ul class="app-menu">
        <li>
            <a class="app-menu__item {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="app-menu__icon fa fa-dashboard"></i>
                <span class="app-menu__label">Админка</span>
            </a>
        </li>
        <li class="treeview {{ Request::is('admin/products*') ||
                               Request::is('admin/categories*') ||
                               Request::is('admin/orders*') ||
                               Request::is('admin/delivery*') ||
                               Request::is('admin/payment*') ||
                               Request::is('admin/attributes*') ||
                               Request::is('admin/pages*') ||
                               Request::is('admin/payment-status*') ||
                               Request::is('admin/slider*') ||
                               Request::is('admin/users*') ||
                               Request::is('admin/orders*') ? 'is-expanded' : '' }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fas fa-store"></i>
                <span class="app-menu__label">Магазин</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu ">

                <li>
                    <a class="treeview-item {{ Request::is('admin/products*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="app-menu__icon fa fa-shopping-bag"></i>
                        <span class="app-menu__label">Продукты</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/categories*') ? 'active' : '' }}"
                       href="{{ route('admin.categories.index') }}">
                        <i class="app-menu__icon fa fa-tags"></i>
                        <span class="app-menu__label">Категории</span>
                    </a>
                </li>
               {{-- <li>
                    <a class="treeview-item "
                       href="#">
                        <i class="app-menu__icon fas fa-code-branch"></i>
                        <span class="app-menu__label">Заведения</span>
                    </a>
                </li>--}}
                <li>
                    <a class="treeview-item {{ Request::is('admin/users*') ? 'active' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <i class="app-menu__icon fa fa-user"></i>
                        <span class="app-menu__label"> Покупатели</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/orders*') ? 'active' : '' }}"
                       href="{{ route('admin.orders.index') }}">
                        <i class="app-menu__icon fas fa-shopping-cart"></i>
                        <span class="app-menu__label"> Заказы</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/delivery*') ? 'active' : '' }}"
                       href="{{ route('admin.delivery.index') }}">
                        <i class="app-menu__icon fas fa-truck"> </i>
                        <span class="app-menu__label"> Способы доставки</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/payment*') && !Request::is('admin/payment-status*') ? 'active' : '' }}"
                       href="{{ route('admin.payment.index') }}">
                        <i class="app-menu__icon fas fa-wallet"></i>
                        <span class="app-menu__label"> Способы оплаты</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/pages*') ? 'active' : '' }}"
                       href="{{ route('admin.pages.index') }}">
                        <i class="app-menu__icon far fa-file-alt"></i>
                        <span class="app-menu__label"> Инф. страницы</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/slider*') ? 'active' : '' }}"
                       href="{{ route('admin.slider.index') }}">
                        <i class="app-menu__icon fab fa-slideshare"></i>
                        <span class="app-menu__label"> Слайдер</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin/payment-status*') ? 'active' : '' }}"
                       href="{{ route('admin.payment-status.index') }}">
                        <i class="app-menu__icon fas fa-money-bill-alt"></i>
                        <span class="app-menu__label"> Статусы оплаты</span>
                    </a>
                </li>
                <li>
                    <a class="treeview-item {{ Request::is('admin.attributes*') ? 'active' : '' }}" href="{{ route('admin.attributes.index') }}">
                        <i class="app-menu__icon fa fa-th"></i>
                        <span class="app-menu__label">Аттрибуты</span>
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a class="app-menu__item {{ Request::is('admin/settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                <i class="app-menu__icon fa fa-cogs"></i>
                <span class="app-menu__label"> Настройки</span>
            </a>
        </li>
    </ul>
</aside>
