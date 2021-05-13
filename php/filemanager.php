<?php

    require_once 'db.php';

    function parseXML($path) {
        $xml=simplexml_load_file($path);
        if(!$xml) return false;
        $cnt=count($xml->product);
        $db=Database::getInstance();
        $error=false;
        for($i=0; $i<$cnt; ++$i) {
            $prod_id=intval($xml->product[$i]->id); $db->cleanData($prod_id);
            $prod_title=$xml->product[$i]->title; $db->cleanData($prod_title);
            $prod_body_html=$xml->product[$i]->{'body-html'}; $db->cleanData($prod_body_html);
            $prod_vendor=$xml->product[$i]->vendor; $db->cleanData($prod_vendor);
            $prod_product_type=$xml->product[$i]->{'product-type'}; $db->cleanData($prod_product_type);
            $prod_created_at=$xml->product[$i]->{'created-at'}; $db->cleanData($prod_created_at); $prod_created_at=date('Y-m-d H:i:s', strtotime($prod_created_at));
            $prod_handle=$xml->product[$i]->handle; $db->cleanData($prod_handle);
            $prod_published_scope=$xml->product[$i]->{'published-scope'}; $db->cleanData($prod_published_scope);
            $prod_tags=$xml->product[$i]->tags; $db->cleanData($prod_tags);   // tags should be in a different table maybe to enhance speed of search by tag if required
            $prod_img_id=$xml->product[$i]->image->id; $db->cleanData($prod_img_id); // image should be in a different table to support multiple images if required otherwise it is better to be in same table
            $prod_img_created_at=$xml->product[$i]->image->{'created-at'}; $db->cleanData($prod_img_created_at); $prod_img_created_at=date('Y-m-d H:i:s', strtotime($prod_img_created_at));
            $prod_img_updated_at=$xml->product[$i]->image->{'updated-at'}; $db->cleanData($prod_img_updated_at); $prod_img_updated_at=date('Y-m-d H:i:s', strtotime($prod_img_updated_at));
            $prod_img_width=$xml->product[$i]->image->width; intval($prod_img_width);
            $prod_img_height=$xml->product[$i]->image->height; intval($prod_img_height);
            $prod_img_src=$xml->product[$i]->image->src; $db->cleanData($prod_img_src);
            
            $db->execute("INSERT INTO `tbl_product` (
                `prod_id`, 
                `prod_title`, 
                `prod_body_html`, 
                `prod_vendor`, 
                `prod_type`, 
                `prod_created_at`, 
                `prod_handle`, 
                `prod_published_scope`, 
                `prod_tags`, 
                `prod_img_id`, 
                `prod_img_created_at`,
                `prod_img_updated_at`,
                `prod_img_width`,
                `prod_img_height`,
                `prod_img_src`
                ) 
                VALUES
                (
                    ${prod_id}, 
                    '${prod_title}', 
                    '${prod_body_html}',
                    '${prod_vendor}',
                    '${prod_product_type}',
                    '".$prod_created_at."',
                    '${prod_handle}',
                    '${prod_published_scope}',
                    '${prod_tags}',
                    '${prod_img_id}',
                    '".$prod_img_created_at."',
                    '".$prod_img_updated_at."',
                    ${prod_img_width},
                    ${prod_img_height},
                    '${prod_img_src}'
                ) ON DUPLICATE KEY UPDATE 
                    `prod_id`=${prod_id}, 
                    `prod_title`='${prod_title}', 
                    `prod_body_html`='${prod_body_html}',
                    `prod_vendor`='${prod_vendor}',
                    `prod_type`='${prod_product_type}',
                    `prod_created_at`='".$prod_created_at."',
                    `prod_handle`='${prod_handle}',
                    `prod_published_scope`='${prod_published_scope}',
                    `prod_tags`='${prod_tags}',
                    `prod_img_id`='${prod_img_id}',
                    `prod_img_created_at`='".$prod_img_created_at."',
                    `prod_img_updated_at`='".$prod_img_updated_at."',
                    `prod_img_width`=${prod_img_width},
                    `prod_img_height`=${prod_img_height},
                    `prod_img_src`='${prod_img_src}';"
            );
            error_log("INSERT INTO `tbl_product` (
                `prod_id`, 
                `prod_title`, 
                `prod_body_html`, 
                `prod_vendor`, 
                `prod_type`, 
                `prod_created_at`, 
                `prod_handle`, 
                `prod_published_scope`, 
                `prod_tags`, 
                `prod_img_id`, 
                `prod_img_created_at`,
                `prod_img_updated_at`,
                `prod_img_width`,
                `prod_img_height`,
                `prod_img_src`
                ) 
                VALUES
                (
                    ${prod_id}, 
                    '${prod_title}', 
                    '${prod_body_html}',
                    '${prod_vendor}',
                    '${prod_product_type}',
                    '".$prod_created_at."',
                    '${prod_handle}',
                    '${prod_published_scope}',
                    '${prod_tags}',
                    '${prod_img_id}',
                    '".$prod_img_created_at."',
                    '".$prod_img_updated_at."',
                    ${prod_img_width},
                    ${prod_img_height},
                    '${prod_img_src}'
                ) ON DUPLICATE KEY UPDATE 
                    `prod_id`=${prod_id}, 
                    `prod_title`='${prod_title}', 
                    `prod_body_html`='${prod_body_html}',
                    `prod_vendor`='${prod_vendor}',
                    `prod_type`='${prod_product_type}',
                    `prod_created_at`='".$prod_created_at."',
                    `prod_handle`='${prod_handle}',
                    `prod_published_scope`='${prod_published_scope}',
                    `prod_tags`='${prod_tags}',
                    `prod_img_id`='${prod_img_id}',
                    `prod_img_created_at`='".$prod_img_created_at."',
                    `prod_img_updated_at`='".$prod_img_updated_at."',
                    `prod_img_width`=${prod_img_width},
                    `prod_img_height`=${prod_img_height},
                    `prod_img_src`='${prod_img_src}';");
            $error|=($db->getLastError()!=="")||($db->getAffectedRows()==0);
        }

        return !$error;
    }

    function parseCSV($path, $skipTitles=true) {
        
        $error=false;
        $empty=true;
        // assuming the CSV fields order is preserved (0=handle. 1=location, 2=amount).
        if (($handle = fopen($path, "r"))) {
            $db=Database::getInstance();
            while ((($data = fgetcsv($handle, 1024, ";")) !== FALSE)) {
                if($skipTitles) { $skipTitles=false; continue; }
                if($data==[null]) continue; /// empty line just ignore
                $error=(count($data)!=3); if($error) break;
                $empty=false;
                $error=!is_numeric($data[2]); if($error) break;
                $amt=is_numeric($data[2])?floatval($data[2]):0.0;
                $loc=$data[1]; $db->cleanData($loc);
                $hdl=$data[0]; $db->cleanData($hdl);

                // make a commit rollback statement to rollback in case an item failed (rollback would consiuder the current file being parsed invalud so maybe we can ignore only 
                // faiked items and give the user a hint about some fails
                $db->execute("INSERT INTO `tbl_inventory` (handle, location, amount) VALUES ('${hdl}', '${loc}', ${amt}) ON DUPLICATE KEY UPDATE location='${loc}', amount=${amt}");
                $error|=($db->getLastError()!="");
            }
            fclose($handle);
        }
        return !$error && !$empty;
    }

    function handleUploadedFile($files, $index) {
        $tmpPath = tmpfile() . md5(time()) . basename($files["name"][$index]);
        $ext = strtolower(pathinfo($tmpPath, PATHINFO_EXTENSION));
        if (file_exists($tmpPath))
            unlink($tmpPath);
        $path=(($ext == "xml") || ($ext == "csv")) && move_uploaded_file($files["tmp_name"][$index], $tmpPath)?$tmpPath:null;
        if(!$path)return false;
        $res=false;
        if($ext=="xml")
            $res=parseXML($path);
        else
            $res=parseCSV($path);
        unlink($path);
        return $res;
    }