<?php
function yas_register_test_post_type()
{
    $labels = array(
        'name'                  => 'آزمون',
        'singular_name'         => 'test',
        'menu_name'             => 'سوالات آزمون',
        'name_admin_bar'        => 'test',
        'add_new'               => 'افزودن سوال آزمون جدید',
        'add_new_item'          =>  'اضافه کردن سوال جدید',
        'new_item'              => 'سوال جدید',
        'edit_item'             => 'ویرایش سوال',
        'view_item'             => 'مشاهده',
        'all_items'             => 'همه سوالات آزمون',
        'search_items'          => 'جستجو',
        'parent_item_colon'     => 'والد سوال:',
        'not_found'             => 'سوالی پیدا نشد.',
        'not_found_in_trash'    => 'سوالی در زباله دان پیدا نشد.',
        'featured_image'        => 'تصویر شاخص',
        'set_featured_image'    =>  'انتخاب تصیور شاخص',
        'remove_featured_image' => 'حذف تصویر شاخص',
        'use_featured_image'    => 'استفاده به عنوان تصویر شاخص',
        'archives'              => 'آرشیو سوالات',
        'insert_into_item'      => 'افزودن به سوالات',
        'uploaded_to_this_item' => 'آپلود',
        'filter_items_list'     => 'فیلتر لیست سوالات',
        'items_list_navigation' => 'پیمایش لیست سوالات',
        'items_list'            => 'لیست سوالات'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'test'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('editor', 'author', 'title'),

    );

    register_post_type('test', $args);
}
function yas_register_test_taxonomy()
{
    $labels = array(
        'name'              => 'دسته بندی ها',
        'singular_name'     => 'دسته بندی',
        'search_items'      => 'جستجوی دسته بندی ها',
        'all_items'         => 'همه دسته بندی ها',
        'parent_item'       => 'والد دسته بندی',
        'parent_item_colon' => 'والد دسته بندی',
        'edit_item'         => 'ویرایش دسته بندی',
        'update_item'       => 'بروزرسانی دسته بندی',
        'add_new_item'      => 'افزودن دسته بندی جدید',
        'new_item_name'     => 'نام دسته بندی جدید',
        'menu_name'         => 'دسته ها',
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'test'),
    );
    register_taxonomy('test-cat', 'test', $args);
    unset($args);
    unset($labels);
    $labels = array(
        'name'              => 'برچسب ها',
        'singular_name'     => 'برچسب',
        'all_items'         => 'همه برچسب ها',
        'parent_item'       => 'والد برچسب',
        'parent_item_colon' => 'والد برچسب',
        'edit_item'         => 'ویایش برچسب',
        'update_item'       => 'بروزرسانی برچسب',
        'add_new_item'      => 'افزودن برچسب جدید',
        'new_item_name'     => 'نام برچسب جدید',
        'menu_name'         => 'برچسب ها',
    );

    $args = array(
        'hierarchical'          => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'test-tag'),
    );

    register_taxonomy('test-tag', 'test', $args);
}
add_action('init', 'yas_register_test_taxonomy');
add_action('init', 'yas_register_test_post_type');
