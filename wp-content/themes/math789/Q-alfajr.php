<?php /* Template Name: quran-alfajr */


if (!empty($_GET['admin']) && $_GET['admin'] === 'mohammadhosseinbahrani6607') {
    do_shortcode('[show-members]');
} else {
    do_shortcode('[fajr]');
}
