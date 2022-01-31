<?php

    function money_fmt($value)
	{
        if (Session::get('money_format') == 'PC'){
            return number_format(round($value,2),0,',','.');
        }else if (Session::get('money_format') == 'PC2'){
    		return number_format(round($value,2),2,',','.');
    	}else if (Session::get('money_format') == 'CP2'){
    		return number_format(round($value,2),2,'.',',');    	
    	}else{
    		return number_format(round($value,2),2,',','.');
    	}
	}

    function remove_2quotes($subject){
        return str_replace('"', '', $subject);
    }
    
    function remove_1quotes($subject){
        return str_replace('\'', '', $subject);
    }

    function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : folderSize($each);
        }
        return $size;
    }    
    
    function folderSizeS3($folder)
    {
        $s3 = Storage::disk('s3');
        $client=$s3->getDriver()->getAdapter()->getClient();
        $size = 0;
        $objects = $client->getIterator('ListObjects', array(
            "Bucket" => config('filesystems.disks.s3.bucket'),
            "Prefix" => config('filesystems.disks.s3.root').'/'.$folder
        ));
        $i = 0;
        foreach ($objects as $object) {
            $size = $size+$object['Size'];
        }

        return number_format($size / 1048576, 2, '.', '');
        //return formatSizeUnits($size);
    }    

    function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }    

    function folderSizeS3v2($folder)
    {
        $s3 = Storage::disk('s3');
        //$client=$s3->getDriver()->getAdapter()->getClient();
        $size = 0;
        $s3->getObjectInfo(config('filesystems.disks.s3.bucket'), $folder);
        //$objs=$s3->fetchObjs($folder,config('filesystems.disks.s3.bucket'));
        //$size=$s3->folderSizeRawPrettyFromSource($objs);

        return $objInfo['size'];
    }    
    

    function month_letter($month, $format)
    {        
        $month_letter = '';
        switch ($month) 
        {
            case 1:
            ($format=='lg')?$month_letter = 'Enero':$month_letter = 'Ene';
            break;
            case 2:
            ($format=='lg')?$month_letter = 'Febrero':$month_letter = 'Feb';
            break;
            case 3:
            ($format=='lg')?$month_letter = 'Marzo':$month_letter = 'Mar';
            break;
            case 4:
            ($format=='lg')?$month_letter = 'Abril':$month_letter = 'Abr';
            break;
            case 5:
            ($format=='lg')?$month_letter = 'Mayo':$month_letter = 'May';
            break;
            case 6:
            ($format=='lg')?$month_letter = 'Junio':$month_letter = 'Jun';
            break;
            case 7:
            ($format=='lg')?$month_letter = 'Julio':$month_letter = 'Jul';
            break;
            case 8:
            ($format=='lg')?$month_letter = 'Agosto':$month_letter = 'Ago';
            break;
            case 9:
            ($format=='lg')?$month_letter = 'Septiembre':$month_letter = 'Sep';
            break;
            case 10:
            ($format=='lg')?$month_letter = 'Octubre':$month_letter = 'Oct';
            break;
            case 11:
            ($format=='lg')?$month_letter = 'Noviembre':$month_letter = 'Nov';
            break;
            case 12:
            ($format=='lg')?$month_letter = 'Diciembre':$month_letter = 'Dic';
            break;
        }
        return $month_letter;
    }

    function urlS3($file){
      $s3 = \Storage::disk('s3');
      $client = $s3->getDriver()->getAdapter()->getClient();
      $expiry = "+10 minutes";

      $command = $client->getCommand('GetObject', [
          'Bucket' => config('filesystems.disks.s3.bucket'),
          'Key'    => config('filesystems.disks.s3.root').'/'.$file 
      ]);

      $request = $client->createPresignedRequest($command, $expiry);

      return (string) $request->getUri();      
    }
    
    function objS3($file){
        try {
            $s3 = \Storage::disk('s3');
            $client = $s3->getDriver()->getAdapter()->getClient();

            $result = $client->getObject([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key'    => config('filesystems.disks.s3.root').'/'.$file 
            ]);

            header("Content-Type: {$result['ContentType']}");

            return $result['Body'];      
            
        } catch (S3Exception $e) {
            
            return $e->getMessage();
        }

    }
