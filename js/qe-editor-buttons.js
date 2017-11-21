(function() {
     /* Register the buttons */
     tinymce.create('tinymce.plugins.QeEditorButtons', {
          init : function(ed, url) {
               /**
               * Inserts shortcode content
               */
               ed.addButton( 'qe_button_code', {
                    text : '[Код]',
				    image : '/wp-content/plugins/qe-quizext/img/qe-button-code.png',
                    onclick : function() {
					  ed.selection.setContent('<h3>Код:</h3>\n\
<h6>Описание кода</h6>\n\
[qe_form id=' +
  Math.floor((Math.random() * 10000) + 1) +
' lang="cpp|pascal" lang-default="pascal" enable-run="1" input="10\\n20\\n30"]\n\
<pre><code>// C++\n\
#include &lt;iostream>\n\
using namespace std;\n\
\n\
int main () {\n\
\tint x;\n\
\tcin >> x;\n\
\tcout &lt;&lt; x &lt;&lt; endl;\n\
\treturn 0;\n\
}\n\
//////////QE-CODE-SEPARATOR==========\n\
// Паскаль\n\
Program simple;\n\
var x: integer;\n\
begin\n\
\treadln(x);\n\
\twriteln(x);\n\
end.\n\
</code></pre>\n\
[/qe_form]');
                    }
               });
               /**
               * Inserts tabulation
               */
               ed.addButton( 'qe_button_tab', {
                    text : '[Tab]',
				    icon : false,
				 	image : '/wp-content/plugins/qe-quizext/img/qe-button-tab.png',
                    onclick : function() {
					  ed.selection.setContent('\t');
                    }
               });
               /**
               * Adds HTML tag to selected content
               */
               ed.addButton( 'qe_button_example', {
                    text : '[Пример]',
                    icon : false,
				    image : '/wp-content/plugins/qe-quizext/img/qe-button-example.png',
                    cmd: 'qe_button_example_cmd'
               });
               ed.addCommand( 'qe_button_example_cmd', function() {
                    var selected_text = ed.selection.getContent();
				    if (selected_text == '')
					  selected_text = "<p>Пример:</p>\
<p>1: </p>\
<p>2: </p>"; 
                    var return_text = '';
                    return_text = '<div class="block example"><p class="tab">Пример</p>' + selected_text + '</div>';
                    ed.execCommand('mceInsertContent', 0, return_text);
               });
               ed.addButton( 'qe_button_info', {
                    text : '[Инфо]',
                    icon : false,
				    image : '/wp-content/plugins/qe-quizext/img/qe-button-info.png',
                    cmd: 'qe_button_info_cmd'
               });
               ed.addCommand( 'qe_button_info_cmd', function() {
                    var selected_text = ed.selection.getContent();
				    if (selected_text == '')
					  selected_text = "<p>Информация:</p>\
<p>1: </p>\
<p>2: </p>"; 
                    var return_text = '';
                    return_text = '<div class="block info">' + selected_text + '</div>';
                    ed.execCommand('mceInsertContent', 0, return_text);
               });
               ed.addButton( 'qe_button_attention', {
                    text : '[Внимание]',
                    icon : false,
				    image : '/wp-content/plugins/qe-quizext/img/qe-button-attention.png',
                    cmd: 'qe_button_attention_cmd'
               });
               ed.addCommand( 'qe_button_attention_cmd', function() {
                    var selected_text = ed.selection.getContent();
				    if (selected_text == '')
					  selected_text = "<p>Внимание:</p>\
<p>1: </p>\
<p>2: </p>"; 
                    var return_text = '';
                    return_text = '<div class="block attention">' + selected_text + '</div>';
                    ed.execCommand('mceInsertContent', 0, return_text);
               });
                ed.addButton( 'qe_button_definition', {
                    text : '[Опред-е]',
                    icon : false,
				    image : '/wp-content/plugins/qe-quizext/img/qe-button-definition.png',
                    cmd: 'qe_button_definition_cmd'
               });
               ed.addCommand( 'qe_button_definition_cmd', function() {
                    var selected_text = ed.selection.getContent();
				    if (selected_text == '')
					  selected_text = "<p>Определение:</p>\
<p>1: </p>\
<p>2: </p>"; 
                    var return_text = '';
                    return_text = '<div class="block definition">' + selected_text + '</div>';
                    ed.execCommand('mceInsertContent', 0, return_text);
               });
         },
          createControl : function(n, cm) {
               return null;
          },
     });
     /* Start the buttons */
     tinymce.PluginManager.add( 'qe_button_script', tinymce.plugins.QeEditorButtons );
})();