
<div class="container ">

    <div class="section_our_solution mt-5" dir="rtl">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="our_solution_category">
                   <div class="solution_cards_box sol_card_top_3">
                       <div class="solution_card">
                            <div class="hover_color_bubble"></div>
                            <div class="so_top_icon " id="top-icon-notification" >
                                <!-- Add icon here if needed -->
                            </div>
                            <div class="solu_title">
                                <h3>بررسی اعلان ها</h3>
                            </div>
                            <div class="solu_description">
                                <p id="notification-messages">
                                    
                                </p>
                                <button type="button" class="read_more_btn">نمایش اعلان ها</button>
                            </div>
                        </div>
                        <div class="solution_card">
                            <div class="hover_color_bubble">
                            </div>
                            <div class="so_top_icon">
                                <!-- Add icon here if needed -->
                                <i class="bi bi-people fs-1"></i>
                            </div>
                            <div class="solu_title">
                                <h3>مدیریت کاربران</h3>
                            </div>
                            <div class="solu_description">
                                <p>
                                    در این قسمت می توانید کاربر جدید را عضو کنید و نقش های کاربران را مدیریت کنید.
                                </p>
                                <button type="button" class="read_more_btn"
                                    data-direct-url="<?php echo home_url('panel/manageuser') ?>" onclick="cardBtn(event)">ورود</button>
                            </div>
                        </div>
                     
                    </div>
                    <div class="solution_cards_box">
                        <div class="solution_card">
                            <div class="hover_color_bubble"></div>
                            <div class="so_top_icon">
                                <!-- Add icon here if needed -->
                                <i class="bi bi-file-text fs-1"></i>
                            </div>
                            <div class="solu_title">
                                <h3>مدیریت فرم‌ها</h3>
                            </div>
                            <div class="solu_description">
                                <p>
                                    در این قسمت فرم مورد نظر خود را طراحی کنیدو فرم های ساخته شده را مشاهده کنید.
                                </p>
                                <button type="button" class="read_more_btn"
                                    data-direct-url="<?php echo home_url('panel/formmaker') ?>" onclick="cardBtn(event)">ورود</button>
                            </div>
                        </div>
                        <div class="solution_card">
                            <div class="hover_color_bubble"></div>
                            <div class="so_top_icon">
                                <!-- Add icon here if needed -->
                                <i class="bi bi-tools fs-1"></i>
                            </div>
                            <div class="solu_title">
                                <h3>مدیریت تجهیزات</h3>
                            </div>
                            <div class="solu_description">
                                <p>
                                    در این قسمت شما می‌توانید تجهیزات را جستجو کنید اطلاعات را به روز رسانی کنید یا اطلاعات جدیدی برای هر تجهیز وارد کنید

                                </p>
                                <button type="button" class="read_more_btn"
                                    data-direct-url="<?php echo home_url('panel/equipmenttracker') ?>" onclick="cardBtn(event)">ورود</button>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                   
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function cardBtn(event) {
        const url = event.target.getAttribute('data-direct-url')
        window.open(url, '_self');
    }
</script>