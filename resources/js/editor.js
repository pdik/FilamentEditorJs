import Header from "@editorjs/header";
import EditorJS from "@editorjs/editorjs";
import DragDrop from "editorjs-drag-drop";
import ImageTool from "@editorjs/image";
import Quote from "@editorjs/quote";
import Paragraph from '@editorjs/paragraph';
import EditorjsList from "@editorjs/list";
import Warning from '@editorjs/warning';
import Alert from 'editorjs-alert';
import TextVariantTune from '@editorjs/text-variant-tune';
import Marker from '@editorjs/marker';
import AttachesTool from '@editorjs/attaches';
import IndentTune from 'editorjs-indent-tune'
import { IconAlignLeft, IconAlignCenter, IconQuote } from '@codexteam/icons';
import * as EditorJSStyle from "editorjs-style";
import AnchorTune from 'editorjs-anchor';
import NoticeTune from 'editorjs-notice';
import imagehotspot from 'editorjs-imagehotspot';
export default function EditorComponent({state,statePath}){
    return {
        state,
        editor: null,
        init(){
            this.editor = new EditorJS({
             holder: this.$refs.editor,
             data: this.state,
             onChange: () => {
                this.editor.save().then((outputData) => {
                  this.state = outputData;
                });
             },
             onReady: () => {
                new DragDrop(this.editor);
             },
             tools: {
                quote: {
                    class:Quote,
                    settings:{
                        icon:IconQuote
                    }
                },
                 anchorTune: AnchorTune,
                 noticeTune: NoticeTune,
                 Marker: {
                  class: Marker,
                  shortcut: 'CMD+SHIFT+M',
                },
                 imageHotspot: {
                    class: imagehotspot,
                    config:{
                       uploadByFile: (file) => this.uploadImage(file),
                    }
                 },
                 indentTune: {
                    class: IndentTune,
                    // recommended for version based style adjustments
                    version: EditorJS.version,
                     config:{
                            maxIndent: 10,
                            indentSize: 30,
                            multiblock: true,
                            tuneName: 'indentTune',
                     }
                },
                 style: EditorJSStyle.StyleInlineTool,
                 attaches: {
                      class: AttachesTool,
                      config: {
                          uploader: {
                            uploadByFile: (file) => this.uploadImage(file),
                            uploadByUrl: (url) => {
                              return new Promise(async (resolve) => {
                                return fetch(url)
                                  .then((res) => res.blob())
                                  .then((blob) => resolve(this.uploadImage(blob)));
                              });
                            },
                          },
                      }
                    },
                 textVariant: TextVariantTune,
                 warning: {
                  class: Warning,
                  inlineToolbar: true,
                  shortcut: 'CMD+SHIFT+W',
                  config: {
                    titlePlaceholder: 'Title',
                    messagePlaceholder: 'Message',
                  },
                },
                alert: {
                  class: Alert,
                  inlineToolbar: true,
                  shortcut: 'CMD+SHIFT+A',
                  config: {
                    alertTypes: ['primary', 'secondary', 'info', 'success', 'warning', 'danger', 'light', 'dark'],
                    defaultType: 'primary',
                    messagePlaceholder: 'Enter something',
                  },
                },
                image: {
                    class: ImageTool,
                    config: {

                      uploader: {
                        uploadByFile: (file) => this.uploadImage(file),
                        uploadByUrl: (url) => {
                          return new Promise(async (resolve) => {
                            return fetch(url)
                              .then((res) => res.blob())
                              .then((blob) => resolve(this.uploadImage(blob)));
                          });
                        },
                      },
                    },
                },
                paragraph: {
                  class: Paragraph,
                  inlineToolbar: true,
                },
                header:{
                    class:Header,
                    shortcut: 'CMD+SHIFT+H',
                    config: {
                        placeholder: 'Enter a header',
                        levels: [2, 3, 4],
                        defaultLevel: 3
                      }
                },
                 list: {
                  class: EditorjsList,
                  inlineToolbar: true,
                  config: {
                    defaultStyle: 'unordered'
                  },
                },
             },
             tunes: ['textVariant','indentTune','anchorTune','noticeTune'],
            });
        },
        uploadImage: function (blob) {
            return new Promise((resolve) => {
              this.$wire.upload(
                `componentFileAttachments.${statePath}`,
                blob,
                (uploadedFilename) => {
                  this.$wire
                    .getFormComponentFileAttachmentUrl(statePath)
                    .then((url) => {
                      if (!url) {
                        return resolve({
                          success: 0,
                        });
                      }
                      return resolve({
                        success: 1,
                        file: {
                          url: url,
                        },
                      });
                    });
                }
              );
            });
      }
    }
}
