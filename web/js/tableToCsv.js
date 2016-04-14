/**
 * Created by hud on 14/04/16.
 */


(function($){
    $.fn.toCSV = function($link) {
        var data = $(this).first(); //Only one table
        var csvData = [];
        var tmpArr = [];
        var tmpStr = '';
        data.find("tr").each(function() {
            if($(this).find("th").length) {
                $(this).find("th").each(function() {
                    tmpStr = $(this).text().replace(/"/g, '""');
                    tmpArr.push('"' + tmpStr + '"');
                });
                csvData.push(tmpArr);
            } else {
                tmpArr = [];
                $(this).find("td").each(function() {
                    if($(this).text().match(/^-{0,1}\d*\.{0,1}\d+$/)) {
                        tmpArr.push(parseFloat($(this).text()));
                    } else {
                        tmpStr = $(this).text().replace(/"/g, '""');
                        tmpArr.push('"' + tmpStr + '"');
                    }
                });
                csvData.push(tmpArr.join(','));
            }
        });
        var output = csvData.join('\n');
        var uri = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(output);
        $link.attr("href", uri);
    }
})(jQuery);