<?php
class FileUploader
{
  public static function handle($files)
  {
    $uploaded_files = [];

    foreach ($files as $field_id => $file) {
      if ($file['error'] === UPLOAD_ERR_OK) {
        $uploaded = wp_handle_upload($file, ['test_form' => false]);
        if (isset($uploaded['url'])) {
          $uploaded_files[$field_id] = $uploaded['url'];
        } else {
          return new WP_Error('upload_error', 'Failed to upload file.');
        }
      } else {
        return new WP_Error('upload_error', 'File upload error: ' . $file['error']);
      }
    }

    return $uploaded_files;
  }
}
