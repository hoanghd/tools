(function() {
    var _original_template = _.template;
    var _template_helpers = {};
    _.mixin( {
        fn : function( newHelpers ) {
            _.extend( _template_helpers, newHelpers );
        },
        template : function( text, data, settings ) {
            if( data ) {
                _.defaults( data, _template_helpers );
                return _original_template.apply( this, arguments );
            }
            
            var template = _original_template.apply( this, arguments );
            var wrappedTemplate = function( data ) {
                data = _.defaults( {}, data, _template_helpers );
                return template.call( this, data );
            };
            return wrappedTemplate;
        }
    } );
} )();

(function ($) {
    "use strict";
    var Element = {
        textfield: function( name, value, htmlOptions ){
            return this.inputField( 'text', name, value, htmlOptions );
        },
        numberField: function( name, value, htmlOptions ) {
            return this.inputField( 'number', name, value, htmlOptions );
        },
        
        rangeField: function( name, value, htmlOptions ) {
            return this.inputField( 'range', name, value, htmlOptions );
        },
        
        dateField: function( name, value, htmlOptions ) {
            return this.inputField( 'date', name, value, htmlOptions );
        },
        
        timeField: function( name, value, htmlOptions ) {
            return this.inputField( 'time', name, value, htmlOptions );
        },
        
        dateTimeField: function( name, value, htmlOptions ) {
            return this.inputField( 'datetime', name, value, htmlOptions );
        },
        
        dateTimeLocalField: function( name, value, htmlOptions ) {
            return this.inputField( 'datetime-local', name, value, htmlOptions );
        },
        
        weekField: function( name, value, htmlOptions ) {
            return this.inputField( 'week', name, value, htmlOptions );
        },
        
        emailField: function( name, value, htmlOptions) {
            return this.inputField( 'email', name, value, htmlOptions );
        },
        
        telField: function( name, value, htmlOptions ) {
            return this.inputField( 'tel', name, value, htmlOptions );
        },
        
        urlField: function( name, value, htmlOptions ) {
            return this.inputField( 'url', name, value, htmlOptions );
        },
        
        hiddenField: function( name, value, htmlOptions ) {
            return this.inputField( 'hidden', name, value, htmlOptions );
        },
        
        passwordField: function( name, value, htmlOptions ) {
            return this.inputField( 'password', name, value, htmlOptions );
        },
        
        fileField: function( name, value, htmlOptions ) {
            return this.inputField( 'file', name, value, htmlOptions );
        },
        
        textArea: function( name, value, $htmlOptions ) {
            var htmlOptions = $.extend( true, {}, ( $htmlOptions || {} ) , { 'name': name });
            
            if( !_.has(htmlOptions, 'id') ) {
                htmlOptions['id'] = this.toIdByName( name );
            }
                
            return this.tag( 'textarea', htmlOptions, value );
        },
        
        radioButton: function( name, checked, $htmlOptions ) {
            var htmlOptions = _.clone( $htmlOptions );
            htmlOptions = (htmlOptions || {});
            
            if(checked)
                htmlOptions['checked']='checked';
            else
                delete htmlOptions['checked'];
                
            var value   = (htmlOptions['value'] || 1);
            var uncheck = null;
            var hidden  = '';
            
            if( _.has(htmlOptions, 'uncheckValue') ) {
                uncheck = htmlOptions['uncheckValue'];
                delete htmlOptions['uncheckValue'];
            }
            
            if( uncheck !== null ) {
                hidden = this.hiddenField( name, uncheck );
            }
            
            return hidden + this.inputField( 'radio', name, value, htmlOptions );
        },
        
        checkBox: function( name, checked, $htmlOptions ) {
            var htmlOptions = _.clone( $htmlOptions );
            htmlOptions = (htmlOptions || {});
            
            if( checked )
                htmlOptions['checked']='checked';
            else
                delete htmlOptions['checked'];
                
            var value   = ( htmlOptions['value'] || 1 );
            var uncheck = null;
            var hidden  = '';
            
            if( _.has( htmlOptions, 'uncheckValue' ) ) {
                uncheck = htmlOptions['uncheckValue'];
                delete htmlOptions['uncheckValue'];
            }
                
            
            if( uncheck !== null ) {
                hidden = this.hiddenField( name, uncheck );
            }
            
            return hidden + this.inputField( 'checkbox', name, value, htmlOptions );
        },
        
        dropDownList: function( name, value, $htmlOptions, data ) {
            var htmlOptions = $.extend( true, {}, ( $htmlOptions || {} ) , { 'name': name });
            
            if( !_.has(htmlOptions, 'id') ) {
                htmlOptions['id'] = this.toIdByName( name );
            }
                
            var options = "\n" + this.listOptions( value, data, htmlOptions );
            var hidden = '';
            
            if( htmlOptions['multiple'] ) {
                if( htmlOptions['name'].substr(-2) !== '[]' ) {
                    htmlOptions['name'] += '[]';
                }
                
                if( _.has(htmlOptions, 'unselectValue')) {
                    hidden = this.hiddenField( htmlOptions['name'].substr( 0, htmlOptions['name'].length - 2 ), htmlOptions['unselectValue'] );
                    delete htmlOptions['unselectValue'];
                }
            }
            
            return hidden + this.tag( 'select', htmlOptions, options );
        },
        
        listBox: function( name, value, data, $htmlOptions ) {
            var htmlOptions = _.clone( $htmlOptions );
            if( !_.has(htmlOptions, 'size') ) {
                htmlOptions['size'] = 4;
            }
            
            if( htmlOptions['multiple'] ) {
                if( name.substr(-2) !== '[]' ) {
                    name += '[]';
                }
            }
            
            return this.dropDownList( name, value, htmlOptions, data );
        },
        
        checkBoxList: function( name, value, $htmlOptions, data ) {
            var self = this;
            var htmlOptions = _.clone( $htmlOptions );
            var template = ( htmlOptions['template'] || '{input} {label}' );
            var separator = ( htmlOptions['separator'] || this.tag('br') );
            
            delete htmlOptions['template'];
            delete htmlOptions['separator'];
           
            if( name.substr(-2) !=='[]' ) {
                name += '[]';
            }
            
            var labelOptions = (htmlOptions['labelOptions'] || {});
            delete htmlOptions['labelOptions'];
            
            var baseID = ( htmlOptions['baseID'] || this.toIdByName( name ) );
            delete htmlOptions[ 'baseID' ];
            
            var checkAll = true;
            var items = [];
            var id = 0;
            
            _.each(data, function(labelTitle, val){
                var checked = ( ( !_.isArray(value) && value == val ) || ( _.isArray(value) && self.inArray(value, val) ) );
                var checkAll = (checkAll && checked);
                
                htmlOptions['value'] = val;
                htmlOptions['id']    = baseID + '_' + (id++);
                var option           = self.checkBox( name, checked, htmlOptions );
                var beginLabel       = self.openTag( 'label', labelOptions );
                var label            = self.label( labelTitle, htmlOptions['id'], labelOptions );
                var endLabel         = self.closeTag( 'label' );
                
                items.push(template.replaceArray(
                    ['{input}', '{beginLabel}', '{label}', '{labelTitle}', '{endLabel}'],
                    [option, beginLabel, label,  labelTitle, endLabel]
                ));
            });
            
            return items.join(separator);
        },
        
        
        radioButtonList: function( name, value, $htmlOptions, data ) {
            var self = this;
            var htmlOptions = _.clone( $htmlOptions );
            var template  = htmlOptions['template']  || '{input} {label}';
            var separator = htmlOptions['separator'] || this.tag('br');
            
            delete htmlOptions['template'];
            delete htmlOptions['separator'];
            
            var labelOptions = htmlOptions['labelOptions'] || {};
            delete htmlOptions['labelOptions'];
            
            if( htmlOptions['empty'] ) {
                if( !_.isString(htmlOptions['empty']) )
                    htmlOptions['empty'] = { '' : htmlOptions['empty'] };
                
                data = $.extend( true, {}, htmlOptions['empty'], data );
                delete htmlOptions['empty'];
            }
            
            var id = 0;
            var items = [];
            var baseID = htmlOptions['baseID'] || this.toIdByName(name);
            delete htmlOptions['baseID'];
            
            _.each(data, function(labelTitle, val){
                htmlOptions['value']     = value;
                htmlOptions['id']        = baseID + '_' + (id++);
                
                var checked    = (value == val);
                var option     = self.radioButton( name, checked, htmlOptions );
                var beginLabel = self.openTag( 'label', labelOptions );
                var label      = self.label( labelTitle, htmlOptions['id'], labelOptions );
                var endLabel   = self.closeTag('label');
                
                items.push( template.replaceArray(
                    ['{input}', '{beginLabel}', '{label}', '{labelTitle}', '{endLabel}'],
                    [option, beginLabel, label, labelTitle, endLabel]
                ));
            });
            
            return items.join(separator);
        },
        
        listOptions: function(selection, listData, $htmlOptions ) {
            var self = this;
            var content = '';
            var htmlOptions = _.clone( $htmlOptions );
            
            if( htmlOptions['prompt'] ) {
                content += $( '<option></option>' ).val('').html( htmlOptions['prompt'] ).outerHTML() + "\n";
                delete htmlOptions['prompt'];
            }
            
            if( _.has(htmlOptions, 'empty') ) {
                if( _.isString( htmlOptions['empty'] ) ) {
                    htmlOptions['empty'] = {'': htmlOptions['empty']};
                }
                
                _.each(htmlOptions['empty'], function(label, value){
                    content += $( '<option></option>' ).val( value ).html( label ).outerHTML() + "\n";
                });
                
                delete htmlOptions['empty'];
            }
            
            var options = [];
            if( htmlOptions['options'] ) {
                options = htmlOptions['options'];
                delete htmlOptions['options'];
            }
            
            var key = (htmlOptions['key'] || 'primaryKey');
            
            if( _.isArray(selection) ) {
                _.each(selection, function(item, i){
                    if(_.isObject(item)) {
                        selection[i] = item[key];
                    }
                });
            }
            else if( _.isObject(selection) ) {
                selection = selection[key];
            }
            
            _.each(listData, function(value, key){
                if( _.isObject( value ) ) {
                    content += self.tag(
                        'optgroup', 
                        {'label': key},
                        self.listOptions(selection, value, {'options': options})
                    ) + "\n";
                }
                else {
                    var attributes = {'value': key};
                    if( ( !_.isArray( selection ) && ( key == selection ) ) || ( _.isArray( selection ) && self.inArray(selection, key) ))
                        attributes['selected']='selected';
                    
                    if( options[ key ] )
                        attributes = $.extend( true, {}, attributes, options[ key ] );
                    
                    content += self.tag('option', attributes,  value) + "\n";
                }
            });
            
            delete htmlOptions['key'];
            
            return content;
        },
        
        inputField: function( type, name, value, $htmlOptions ){
            var htmlOptions = $.extend( true, {}, ( $htmlOptions || {} ) , { 'type': type, 'value': value, 'name': name });
            
            if( !_.has(htmlOptions, 'id') ) {
                htmlOptions['id'] = this.toIdByName( name );
            }
            
            return this.tag('input', htmlOptions);
        },
        
        label: function(label, $for, $htmlOptions) {
            var htmlOptions = _.clone( $htmlOptions );
            
            if($for===false)
                delete htmlOptions['for'];
            else
                htmlOptions['for'] = $for;
            
            return this.tag( 'label', htmlOptions, label );
        },
        
        tag: function(name, htmlOptions, content){
            return $('<' + name + '/>', (htmlOptions || {}) ).html(content).outerHTML();
        },
        
        openTag: function(name, htmlOptions){
            var html = this.tag(name, htmlOptions);
            return html.substr(0, (html.length - (name.length + 3)));
        },
        
        closeTag: function(name){
            return '</' + name + '>';
        },
        
        toIdByName: function(name) {
            return name.replace('[]', '').replace('][', '_').replace('[', '_').replace('[', '_');
        },
        
        inArray: function(data, val){
            var exists = false;
            
            _.each( data, function( value ){
                if( value == val ) {
                     exists = true;
                }
            });
            
            return exists;
        }
    };
    
    $.fn.outerHTML = function() {
       return (this[0]) ? this[0].outerHTML : ''; 
    };
    
    String.prototype.replaceArray = function(find, replace) {
        var replaceString = this;
        var regex; 
        for (var i = 0; i < find.length; i++) {
            regex = new RegExp(find[i], "g");
            replaceString = replaceString.replace(regex, replace[i]);
        }
        return replaceString;
    };
    
    _.fn({ '$el': Element });
    _.mixin( Element );
}(window.jQuery));