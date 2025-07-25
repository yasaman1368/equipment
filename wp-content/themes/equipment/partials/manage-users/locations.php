<div
    class="tab-pane"
    id="locations"
    role="tabpanel"
    aria-labelledby="account-tab">
    <div class="container bg-light mt-3 rounded p-3">
        <form id="locations-form">
            <div class="mb-3">
                <label class="form-label">نام موقعیت</label>
                <input type="text" class="form-control" name="location" placeholder="نام موقعیت را وارد کنید">
            </div>
            <?php
            wp_nonce_field('add-location', 'add-location-nonce');
            ?>
            <button type="submit" class="btn btn-primary">افزودن</button>
        </form>
        <div class="mt-4">
            <h4>لیست موقعیت‌ها</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>نام موقعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody id="locations-list">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>