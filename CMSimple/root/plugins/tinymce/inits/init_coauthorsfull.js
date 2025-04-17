var tinyConfig = {
    selector: "cmsimple-editor",
    skin: "cmsimple",
    block_formats: "Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;Header 6=h6;Div=div;Paragraph=p;code=code;pre=pre",
    toolbar_items_size: "small",
	font_formats: "Arial=arial,helvetica,sans-serif;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier,monospace;Fraktur Kleist=KleistFraktur,serif;Fraktur Normal=NormalFraktur,serif;Georgia=georgia,times new roman,serif;Helvetica=helvetica,arial,sans-serif;RobotoCondensed=RobotoCondensed,Arial,sans-serif;Tahoma=tahoma,arial,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,sans-serif",
    fontsize_formats: "8px 10px 12px 14px 15px 16px 18px 20px 22px 24px 26px 28px 30px 32px 36px 40px 48px",
    entity_encoding : "named",
	entities : "160,nbsp,173,shy,8201,thinsp,8204,zwnj,8205,zwj",
    element_format : "html",
    extended_valid_elements: "script[type|language|src],i[class]",
    autosave_ask_before_unload: true,
    plugins: ["autosave advlist autolink lists link image media charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking nospace shy save table contextmenu directionality emoticons template paste textcolor colorpicker textpattern importcss help"
    ],
	table_default_attributes: {},
	table_default_styles: {},
	table_responsive_width: true,
	image_description: true,
	image_title: true,
    image_dimensions: false,
    image_advtab: true,
	image_caption: true,
	keep_styles: false,
	object_resizing : 'img',
/*
	image_class_list: [
        {title: 'Keine Klasse', value: ''},
        {title: 'left border', value: 'tmce_left tmce_border'},
        {title: 'left no border', value: 'tmce_left tmce_noborder'},
        {title: 'right border', value: 'tmce_right tmce_border'},
        {title: 'right no border', value: 'tmce_right tmce_noborder'},
        {title: 'centered border', value: 'tmce_centered tmce_border'},
        {title: 'centered no border', value: 'tmce_centered tmce_noborder'},
    ],
*/
    importcss_groups: [
        {title: "Table styles", filter: /^(td|tr|table)\./},
        {title: "Block styles", filter: /^(div|p|ul|ol|h1|h2|h3|h4|h5|h6)\./},
        {title: "Image styles", filter: /^(img)\./},
        {title: "Other styles"}
    ],
    menu: {
        edit: {title: "Edit", items: "cut copy paste pastetext searchreplace | undo redo | selectall"},
		format: {title: "Format", items: "formats | bold italic underline strikethrough superscript subscript | removeformat"},
        insert: {title: "Insert", items: "image link anchor | charmap hr nonbreaking nospace shy"},
        view: {title: "View", items: "code fullscreen | visualaid visualblocks visualchars"},
        table: {title: "Table", items: "inserttable tableprops deletetable | cell row column"}
    },
    menubar: "edit format insert view table",
    toolbar: "save code fullscreen | formatselect styleselect | fontselect fontsizeselect | searchreplace cut copy paste pastetext | alignleft aligncenter alignright alignjustify | bullist numlist | blockquote | outdent indent |  bold italic | underline strikethrough | superscript subscript | forecolor backcolor | removeformat | link unlink anchor | image emoticons | charmap hr nonbreaking nospace shy | table | visualblocks | undo redo | help",
	relative_urls: false,
	remove_script_host: false
};