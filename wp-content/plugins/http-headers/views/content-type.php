<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
    <th scope="row">Content-Type
        <p class="description"><?php esc_html_e('The Content-Type entity header is used to indicate the media type of the resource. In responses, a Content-Type header tells the client what the content type of the returned content actually is. Browsers will do MIME sniffing in some cases and will not necessarily follow the value of this header; to prevent this behavior, the header X-Content-Type-Options can be set to nosniff.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
    </th>
    <td>
        <fieldset>
            <legend class="screen-reader-text">Content-Type</legend>
            <?php
            $http_headers_content_type = get_option('hh_content_type', 0);
            foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
            {
                ?><p><label><input type="radio" class="http-header" name="hh_content_type" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_content_type, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
            }
            ?>
        </fieldset>
    </td>
    <td>
        <?php settings_fields('http-headers-cty'); ?>
        <?php do_settings_sections('http-headers-cty'); ?>
        <?php
        $http_headers_content_type_value = get_option('hh_content_type_value');
        if (!$http_headers_content_type_value) {
            $http_headers_content_type_value = array();
        }

        $http_headers_map = array(
            'eot'   => 'application/vnd.ms-fontobject',
            'otf'   => 'application/x-font-opentype',
            'svg'   => 'image/svg+xml',
            'ttf'   => 'application/x-font-ttf',
            'woff'  => 'application/font-woff',
            'woff2' => 'application/font-woff2',
            'jsonp' => 'application/javascript',
        );
        ?>
        <table>
            <tbody>
                <tr>
                    <td></td>
                    <td><strong><?php esc_html_e('Extension', 'http-headers'); ?></strong></td>
                    <td><strong><?php esc_html_e('Media type', 'http-headers'); ?></strong></td>
                </tr>
            <?php
            foreach ($http_headers_map as $http_headers_ext => $http_headers_media_type)
            {
                ?>
                <tr>
                    <td>
                        <input type="checkbox" class="http-header-value"
                            name="hh_content_type_value[<?php echo esc_attr($http_headers_ext); ?>]"
                            value="<?php echo esc_attr($http_headers_media_type); ?>"<?php
                                echo !(array_key_exists($http_headers_ext, $http_headers_content_type_value) && $http_headers_content_type_value[$http_headers_ext] == $http_headers_media_type) ? NULL : ' checked';
                                echo $http_headers_content_type == 1 ? NULL : ' readonly'; ?>></td>
                    <td>.<?php echo esc_html($http_headers_ext); ?></td>
                    <td><?php echo esc_html($http_headers_media_type); ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </td>
</tr>