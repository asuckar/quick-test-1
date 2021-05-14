const Globals = {
    UPLOAD_API:"http://localhost:8888/nu3/php/upload.php",  // better to have https working but in localhost it is a bit ugly.and  maybe a rewrite here to have a beautified URL like  http://example.com/upload (better for many reasons)
    DEBUG: false,
    ITEM_NOT_FOUND: new Error('Item not found'),
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
            if (idx > -1) array.splice(idx, 1); else throw Globals.ITEM_NOT_FOUND;
            return array;
        }
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = Globals;
}