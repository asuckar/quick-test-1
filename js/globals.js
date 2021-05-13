const Globals = {
    UPLOAD_API:"http://localhost:8888/nu3/php/upload.php",  // better to have https working but in localhost it is a bit ugly.and  maybe a rewrite here to have a beautified URL like  http://example.com/upload (better for many reasons)
    DEBUG: false,
    FUNCTIONS: {
        log: function(value) {
            if(!Globals.DEBUG) return;
            console.log(value);
        },
        removeItemFromFileArray :function(array, item) {
            var idx = -1;
            for(var i=0; i<array.length; ++i)
                if((array[i]["name"] == item)) {
                    idx=i;
                    break;
                }
            if (idx > -1) array.splice(idx, 1);
            return array;
        }
    }
};