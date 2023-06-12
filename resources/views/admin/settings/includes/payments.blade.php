<div class="tile">
    <form action="{{ route('admin.settings.update') }}" method="POST" role="form">
        @csrf
        <h3 class="tile-title">Настройки оплаты</h3>
        <hr>
        <div class="tile-body">
            <div class="form-group">
                <label class="control-label" for="wayforpay_test">Тема</label>
                <select name="wayforpay_test" id="wayforpay_test" class="form-control">
                    <option value="1" {{ (config('settings.wayforpay_test')) == 1 ? 'selected' : '' }}>Включен</option>
                    <option value="0" {{ (config('settings.wayforpay_test')) == 0 ? 'selected' : '' }}>Выключен</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label" for="wayforpay_domain">Wayforpay сайт мерчанта</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите сайт мерчанта"
                    id="wayforpay_domain"
                    name="wayforpay_domain"
                    value="{{ config('settings.wayforpay_domain') }}"
                />
            </div>
            <div class="form-group pb-2">
                <label class="control-label" for="wayforpay_account">Wayforpay аккаунт мерчанта</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите аккаунт мерчанта"
                    id="wayforpay_account"
                    name="wayforpay_account"
                    value="{{ config('settings.wayforpay_account') }}"
                />
            </div>
            <div class="form-group pb-2">
                <label class="control-label" for="wayforpay_secret_key">Wayforpay секретный ключ</label>
                <input
                    class="form-control"
                    type="text"
                    placeholder="Введите секретный ключ мерчанта"
                    id="wayforpay_secret_key"
                    name="wayforpay_secret_key"
                    value="{{ config('settings.wayforpay_secret_key') }}"
                />
            </div>
            <hr>

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
