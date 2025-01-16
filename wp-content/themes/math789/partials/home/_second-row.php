<div class="row text-center mt-1 g-1">
    <div class="col-sm-5">
        <div class="content-sections bg-white rounded shadow-sm container-shadow h-100 p-2">
            <div id="pic-direction" class="carousel slide" data-bs-ride="carousel">
                <ol class="carousel-indicators">
                    <li
                        data-bs-target="#pic-direction"
                        data-bs-slide-to="0"
                        class="active"
                        aria-current="true"
                        aria-label="First slide"></li>
                    <li
                        data-bs-target="#pic-direction"
                        data-bs-slide-to="1"
                        aria-label="Second slide"></li>
                    <li
                        data-bs-target="#pic-direction"
                        data-bs-slide-to="2"
                        aria-label="Third slide"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img
                            src="<?php echo get_template_directory_uri() ?>/assets/home/images/creating-exam.png"
                            class="w-100 d-block"
                            alt="creating-exam" />
                        <div class="carousel-caption d-none d-md-block">
                            <h3>طراحی آزمون</h3>
                            <p>آزمون دلخواه بر اساس فیلتر فصل و تعداد سوال طراحی کنید </p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img
                            src="<?php echo get_template_directory_uri() ?>/assets/home/images/exam-view.png"
                            class="w-100 d-block"
                            alt="exam-view" />
                        <div class="carousel-caption d-none d-md-block">
                            <h3>برگه آزمون</h3>
                            <p>نمایش سوالات و گزینه ها در یک صفحه</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img
                            src="<?php echo get_template_directory_uri() ?>/assets/home/images/show-results.png"
                            class="w-100 d-block"
                            alt="show-results" />
                        <div class="carousel-caption d-none d-md-block">
                            <h3>نمایش عملکرد</h3>
                            <p>عملکرد و نحوه پاسخ دانش آموز را مشاهده کنید </p>
                        </div>
                    </div>
                </div>
                <button
                    class="carousel-control-prev"
                    type="button"
                    data-bs-target="#pic-direction"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">قبلی</span>
                </button>
                <button
                    class="carousel-control-next"
                    type="button"
                    data-bs-target="#pic-direction"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">بعدی</span>
                </button>
            </div>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="content-sections bg-white rounded shadow-sm container-shadow p-2 " style="height: auto;">
            <div class="oqlidus-content rounded">
                <span class="aniBlock"></span>
                <a href="<?php echo home_url('index') ?>">
                    <div class="bg-light rounded-5 title-oqlidus-game">
                        <i class="bi bi-arrow-left-short fw-bolder"></i>
                        <span> بازی هندسه اقلیدسی </span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>