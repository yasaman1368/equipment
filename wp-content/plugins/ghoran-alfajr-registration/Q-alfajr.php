<?php /* Template Name: quran-alfajr */

if (!empty($_GET['submit']) && $_GET['submit'] === 'QF') {
    do_shortcode('[fajr]');
} elseif (!empty($_GET['admin']) && $_GET['admin'] === 'mohammadhosseinbahrani6607') {
    do_shortcode('[show-members]');
} else {
    do_shortcode('[quran-alfajr-home]');
}
