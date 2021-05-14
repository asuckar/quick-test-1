<?php

    require_once 'db.php';
    require_once 'Exceptions.php';

    function parseXML($path) {
        $xml=null;
        try {
            $xml=simplexml_load_file($path);
        }
        catch(Exception $e) {
            $xml=null;
        }
        if(!$xml) throw new InvalidXMLException("$path is not an invalid XML file");
        $cnt=$xml->product->count();
        if(!$cnt) throw new InvalidInputException("$path is empty or invalid");
        $db=Database::getInstance();

        for($i=0; $i<$cnt; ++$i) {
            $cprod=$xml->product[$i];
            $prod_id=$cprod->id;
            if(!is_numeric($prod_id.'')) throw new InvalidFileContentException("Invalid datetime value for 'product -> id'");
            $prod_id=intval($prod_id);
            $prod_title=$cprod->title; $db->cleanData($prod_title);
            $prod_body_html=$cprod->{'body-html'}; $db->cleanData($prod_body_html);
            $prod_vendor=$cprod->vendor; $db->cleanData($prod_vendor);
            $prod_product_type=$cprod->{'product-type'}; $db->cleanData($prod_product_type);
            
            $prod_created_at=$cprod->{'created-at'};
            $dt=strtotime($prod_created_at);
            if($dt===false) throw new InvalidFileContentException("Invalid datetime value for 'product -> created-at'");
            $prod_created_at=date('Y-m-d H:i:s', $dt);
            $db->cleanData($prod_created_at); 

            $prod_handle=$cprod->handle; $db->cleanData($prod_handle);
            $prod_published_scope=$cprod->{'published-scope'}; $db->cleanData($prod_published_scope);
            $prod_tags=$cprod->tags; $db->cleanData($prod_tags);   // tags should be in a different table maybe to enhance speed of search by tag if required
            $prod_img_id=$cprod->image->id;
            if(!is_numeric($prod_img_id.'')) throw new InvalidFileContentException("Invalid datetime value for 'product -> image -> id'");
            $prod_img_id=intval($prod_img_id);
            
            $prod_img_created_at=$cprod->image->{'created-at'};
            $dt=strtotime($prod_img_created_at);
            if($dt===false) throw new InvalidFileContentException("Invalid datetime value for 'product -> image -> created-at'");
            $prod_img_created_at=date('Y-m-d H:i:s', $dt);
            $db->cleanData($prod_img_created_at); 
            
            $prod_img_updated_at=$cprod->image->{'updated-at'};
            $dt=strtotime($prod_img_updated_at);
            if($dt===false) throw new InvalidFileContentException("Invalid datetime value for 'product -> image -> updated-at'");
            $prod_img_updated_at=date('Y-m-d H:i:s', $dt);
            $db->cleanData($prod_img_updated_at); 
            
            $prod_img_width=$cprod->image->width;
            if(!is_numeric($prod_img_width.'')) throw new InvalidFileContentException("Invalid integer value for 'product -> image -> width'=::".$prod_img_width."::");
            $prod_img_width=intval($prod_img_width);

            $prod_img_height=$cprod->image->height;
            if(!is_numeric($prod_img_height.'')) throw new InvalidFileContentException("Invalid integer value for 'product -> image -> height'=".$prod_img_height);
            $prod_img_height=intval($prod_img_height);

            $prod_img_src=$cprod->image->src; $db->cleanData($prod_img_src);
            
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
                    '${prod_created_at}',
                    '${prod_handle}',
                    '${prod_published_scope}',
                    '${prod_tags}',
                    '${prod_img_id}',
                    '${prod_img_created_at}',
                    '${prod_img_updated_at}',
                    ${prod_img_width},
                    ${prod_img_height},
                    '${prod_img_src}'
                ) ON DUPLICATE KEY UPDATE 
                    `prod_id`=${prod_id}, 
                    `prod_title`='${prod_title}', 
                    `prod_body_html`='${prod_body_html}',
                    `prod_vendor`='${prod_vendor}',
                    `prod_type`='${prod_product_type}',
                    `prod_created_at`='${prod_created_at}',
                    `prod_handle`='${prod_handle}',
                    `prod_published_scope`='${prod_published_scope}',
                    `prod_tags`='${prod_tags}',
                    `prod_img_id`='${prod_img_id}',
                    `prod_img_created_at`='${prod_img_created_at}',
                    `prod_img_updated_at`='${prod_img_updated_at}',
                    `prod_img_width`=${prod_img_width},
                    `prod_img_height`=${prod_img_height},
                    `prod_img_src`='${prod_img_src}';"
            );
            if($db->getLastError()!=="") throw new InvalidMySQLCommandException('Invalid MySQL command');
        }

        return true;
    }

    function parseCSV($path, $skipTitles=true) {

        // assuming the CSV fields order is preserved (0=handle. 1=location, 2=amount).
        $exception=null;
        if (($handle = fopen($path, "r"))) {
            $db=Database::getInstance();
            $data = fgetcsv($handle, 1024, ";");

            if($data === FALSE)
                $exception=new InvalidCSVException("$path is not an invalid inventory CSV file");

            while($data!==FALSE) {
                if($skipTitles) { $skipTitles=false; $data = fgetcsv($handle, 1024, ";"); continue; }
                if($data==[null]) continue; // empty line just ignore                

                if(count($data)!=3) {
                    $exception=new InvalidCSVException("$path is not an invalid inventory CSV file");
                    break;
                }

                if(!is_numeric($data[2].'')) {
                    $exception=new InvalidFileContentException("Invalid numeric value for 'Amount'=".$data[2]);
                    break;
                }
                $amt=floatval($data[2]);
                $loc=$data[1]; $db->cleanData($loc);
                $hdl=$data[0]; $db->cleanData($hdl);

                // make a commit rollback statement to rollback in case an item failed (rollback would consiuder the current file being parsed invalud so maybe we can ignore only 
                // faiked items and give the user a hint about some fails
                $db->execute("INSERT INTO `tbl_inventory` (handle, location, amount) VALUES ('${hdl}', '${loc}', ${amt}) ON DUPLICATE KEY UPDATE location='${loc}', amount=${amt}");
                if($db->getLastError()!=="") {
                    $exception=new InvalidMySQLCommandException('Invalid MySQL command');
                    break;
                }
                $data = fgetcsv($handle, 1024, ";");
            };
            fclose($handle);
        }
        else $exception=new InvalidCSVException("$path is not an invalid CSV file");
        if($exception) throw $exception;
        return true;
    }

    function handleUploadedFile($files, $index) {
        $tmpPath = tmpfile() . md5(time()) . basename($files["name"][$index]);
        $ext = strtolower(pathinfo($tmpPath, PATHINFO_EXTENSION));
        if (file_exists($tmpPath)) unlink($tmpPath);
        if(($ext != "xml") && ($ext != "csv")) throw new InvalidFileFormatException("Unsupported file format"); //better also to check the file mimetype not only extension

        if(move_uploaded_file($files["tmp_name"][$index], $tmpPath))
            $path=$tmpPath;
        else 
            throw new InternalServerException("Cannot move the file specified");
        $res=false;
        $res=($ext=="xml")?parseXML($path):parseCSV($path);
        unlink($path);
        return $res;
    }