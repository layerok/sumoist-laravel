<div class="tile">
    <form action="{{ route('admin.settings.update') }}" method="POST" role="form">
        @csrf
        <h3 class="tile-title">General Settings</h3>
        <hr>
        <div class="tile-body">
            <div class="form-group">
                <label class="control-label" for="theme">Тема</label>
                <select name="theme" id="theme" class="form-control">
                    @foreach($themes as $theme)
                        <option value="{{ $theme }}" {{ (config('settings.theme')) == $theme ? 'selected' : '' }}>{{ $theme }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="control-label" for="site_name">Имя сайта</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите имя сайта"
                    id="site_name"
                    name="site_name"
                    value="{{ config('settings.site_name') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="site_title">Заголовок сайта</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите заголовок сайта"
                    id="site_title"
                    name="site_title"
                    value="{{ config('settings.site_title') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="default_email_address">Электронная почта</label>
                <input
                    class="form-control"
                    type="email"
                    placeholder="Enter store default email address"
                    id="default_email_address"
                    name="default_email_address"
                    value="{{ config('settings.default_email_address') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="phone">Телефон(ы) - Несколько телефонов заполняйте через точку с запятой</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите телефоны"
                    id="phone"
                    name="phone"
                    value="{{ config('settings.phone') }}"
                />
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="control-label" for="start_working">Начало рабочего дня</label>
                        <input
                            class="form-control"
                            type="text"
                            placeholder="Время начала работы"
                            id="start_working"
                            name="start_working"
                            value="{{ config('settings.start_working') }}"
                        />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="control-label" for="finish_working">Конец рабочего дня</label>
                        <input
                            class="form-control"
                            type="text"
                            placeholder="Время конца работы"
                            id="finish_working"
                            name="finish_working"
                            value="{{ config('settings.finish_working') }}"
                        />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label" for="currency_code">Код валюты</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите код валюты"
                    id="currency_code"
                    name="currency_code"
                    value="{{ config('settings.currency_code') }}"
                />
            </div>
            <div class="form-group">
                <label class="control-label" for="currency_symbol">Символ валюты</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите символ валюты"
                    id="currency_symbol"
                    name="currency_symbol"
                    value="{{ config('settings.currency_symbol') }}"
                />
            </div>
        </div>
        <div class="tile-footer">
            <div class="row d-print-none mt-2">
                <div class="col-12 text-right">
                    <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update Settings</button>
                </div>
            </div>
        </div>
    </form>
</div>
