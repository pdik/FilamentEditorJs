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

/**
 * ImageHotspot Tool for Editor.js
 * Allows adding interactive hotspots to images
 */

class ImageHotspot {
  /**
   * Render plugin`s main Element and fill it with saved data
   *
   * @param {object} params - constructor params
   * @param {object} params.data - previously saved data
   * @param {object} params.config - user config for Tool
   * @param {object} params.api - Editor.js API
   * @param {boolean} params.readOnly - read-only mode flag
   */
  constructor({ data, config, api, readOnly }) {
    this.api = api;
    this.readOnly = readOnly;
    this.data = {
      image: data.image || {},
      hotspots: data.hotspots || [],
      caption: data.caption || '',
    };

    this.CSS = {
      wrapper: 'image-hotspot-tool',
      imageContainer: 'image-hotspot-tool__image-container',
      image: 'image-hotspot-tool__image',
      caption: 'image-hotspot-tool__caption',
      hotspot: 'image-hotspot-tool__hotspot',
      hotspotMarker: 'image-hotspot-tool__hotspot-marker',
      hotspotEditor: 'image-hotspot-tool__hotspot-editor',
      hotspotEditorHeader: 'image-hotspot-tool__hotspot-editor-header',
      hotspotEditorTitle: 'image-hotspot-tool__hotspot-editor-title',
      hotspotEditorContent: 'image-hotspot-tool__hotspot-editor-content',
      hotspotEditorActions: 'image-hotspot-tool__hotspot-editor-actions',
      button: 'image-hotspot-tool__button',
      buttonPrimary: 'image-hotspot-tool__button--primary',
      buttonDanger: 'image-hotspot-tool__button--danger',
      uploadButton: 'image-hotspot-tool__upload-button',
      addHotspotButton: 'image-hotspot-tool__add-hotspot-button',
      input: 'image-hotspot-tool__input',
      textarea: 'image-hotspot-tool__textarea',
    };

    this.settings = {
      uploadByFile: config.uploadByFile || undefined,
      uploadByUrl: config.uploadByUrl || undefined,
      additionalRequestData: config.additionalRequestData || {},
      additionalRequestHeaders: config.additionalRequestHeaders || {},
      captionPlaceholder: config.captionPlaceholder || 'Caption (optional)',
      buttonLabels: Object.assign({
        upload: 'Upload an image',
        uploadUrl: 'Upload by URL',
        addHotspot: 'Add Hotspot',
        save: 'Save',
        cancel: 'Cancel',
        remove: 'Remove',
      }, config.buttonLabels || {}),
    };

    this.nodes = {
      wrapper: null,
      imageContainer: null,
      image: null,
      caption: null,
      fileInput: null,
      urlInput: null,
      hotspotEditor: null,
    };

    this.activeHotspotId = null;
    this.addingHotspot = false;
  }

  /**
   * Creates the plugin UI
   * @returns {HTMLElement}
   */
  render() {
    this.nodes.wrapper = this._make('div', [this.CSS.wrapper]);

    // If we have an image, render it with hotspots
    if (this.data.image.url) {
      this._renderImage();
    } else {
      // Otherwise, render the upload UI
      this._renderUploadUI();
    }

    return this.nodes.wrapper;
  }

  /**
   * Renders the image with hotspots
   * @private
   */
  _renderImage() {
    this.nodes.imageContainer = this._make('div', [this.CSS.imageContainer]);
    this.nodes.wrapper.appendChild(this.nodes.imageContainer);

    this.nodes.image = this._make('img', [this.CSS.image]);
    this.nodes.image.src = this.data.image.url;
    this.nodes.image.alt = this.data.image.alt || '';
    this.nodes.imageContainer.appendChild(this.nodes.image);

    // Add caption input
    this.nodes.caption = this._make('div', [this.CSS.caption], {
      contentEditable: !this.readOnly,
      innerHTML: this.data.caption || '',
      placeholder: this.settings.captionPlaceholder,
    });
    this.nodes.wrapper.appendChild(this.nodes.caption);

    // Render existing hotspots
    this.data.hotspots.forEach(hotspot => {
      this._renderHotspot(hotspot);
    });

    // Add "Add Hotspot" button if not in read-only mode
    if (!this.readOnly) {
      const addHotspotButton = this._make('button', [this.CSS.button, this.CSS.buttonPrimary, this.CSS.addHotspotButton], {
        textContent: this.settings.buttonLabels.addHotspot,
        type: 'button',
      });
      this.nodes.wrapper.appendChild(addHotspotButton);

      addHotspotButton.addEventListener('click', () => {
        this.addingHotspot = true;
        this.nodes.image.style.cursor = 'crosshair';

        // Add one-time click listener to the image
        const clickHandler = (e) => {
          const rect = this.nodes.image.getBoundingClientRect();
          const x = ((e.clientX - rect.left) / rect.width) * 100;
          const y = ((e.clientY - rect.top) / rect.height) * 100;

          this._addHotspot(x, y);

          this.addingHotspot = false;
          this.nodes.image.style.cursor = '';
          this.nodes.image.removeEventListener('click', clickHandler);
        };

        this.nodes.image.addEventListener('click', clickHandler);
      });
    }
  }

  /**
   * Renders the upload UI
   * @private
   */
  _renderUploadUI() {
    const uploadButton = this._make('button', [this.CSS.button, this.CSS.buttonPrimary, this.CSS.uploadButton], {
      textContent: this.settings.buttonLabels.upload,
      type: 'button',
    });
    this.nodes.wrapper.appendChild(uploadButton);

    // Create file input
    this.nodes.fileInput = this._make('input', [], {
      type: 'file',
      accept: 'image/*',
      style: 'display: none',
    });
    this.nodes.wrapper.appendChild(this.nodes.fileInput);

    // Handle file upload
    uploadButton.addEventListener('click', () => {
      this.nodes.fileInput.click();
    });

    this.nodes.fileInput.addEventListener('change', async (e) => {
      if (e.target.files && e.target.files.length > 0) {
        const file = e.target.files[0];

        try {
          const response = await this.settings.uploadByFile(file);

          this.data.image = {
            url: response.file.url,
            alt: file.name,
          };

          // Clear the wrapper and render the image
          this.nodes.wrapper.innerHTML = '';
          this._renderImage();
        } catch (error) {
          console.error('Image upload failed', error);
          this.api.notifier.show({
            message: 'Image upload failed',
            style: 'error',
          });
        }
      }
    });

    // Add URL upload option if enabled
    if (this.settings.uploadByUrl) {
      const urlUploadWrapper = this._make('div', [], {
        style: 'margin-top: 10px;',
      });

      this.nodes.urlInput = this._make('input', [this.CSS.input], {
        placeholder: 'Image URL',
        type: 'url',
      });

      const urlUploadButton = this._make('button', [this.CSS.button, this.CSS.buttonPrimary], {
        textContent: this.settings.buttonLabels.uploadUrl,
        type: 'button',
        style: 'margin-left: 10px;',
      });

      urlUploadWrapper.appendChild(this.nodes.urlInput);
      urlUploadWrapper.appendChild(urlUploadButton);
      this.nodes.wrapper.appendChild(urlUploadWrapper);

      urlUploadButton.addEventListener('click', async () => {
        const url = this.nodes.urlInput.value.trim();

        if (!url) return;

        try {
          const response = await this.settings.uploadByUrl(url);

          this.data.image = {
            url: response.file.url,
            alt: url.split('/').pop() || '',
          };

          // Clear the wrapper and render the image
          this.nodes.wrapper.innerHTML = '';
          this._renderImage();
        } catch (error) {
          console.error('Image upload by URL failed', error);
          this.api.notifier.show({
            message: 'Image upload by URL failed',
            style: 'error',
          });
        }
      });
    }
  }

  /**
   * Renders a hotspot on the image
   * @param {object} hotspot - hotspot data
   * @private
   */
  _renderHotspot(hotspot) {
    const hotspotElement = this._make('div', [this.CSS.hotspot], {
      style: `left: ${hotspot.x}%; top: ${hotspot.y}%;`,
      'data-id': hotspot.id,
    });

    const hotspotMarker = this._make('div', [this.CSS.hotspotMarker]);
    hotspotElement.appendChild(hotspotMarker);

    this.nodes.imageContainer.appendChild(hotspotElement);

    // Add click handler if not in read-only mode
    if (!this.readOnly) {
      hotspotElement.addEventListener('click', (e) => {
        e.stopPropagation();
        this._editHotspot(hotspot.id);
      });
    }
  }

  /**
   * Adds a new hotspot at the specified coordinates
   * @param {number} x - x coordinate in percentage
   * @param {number} y - y coordinate in percentage
   * @private
   */
  _addHotspot(x, y) {
    const hotspot = {
      id: this._generateId(),
      x,
      y,
      title: '',
      content: '',
    };

    this.data.hotspots.push(hotspot);
    this._renderHotspot(hotspot);
    this._editHotspot(hotspot.id);
  }

  /**
   * Opens the hotspot editor for the specified hotspot
   * @param {string} id - hotspot id
   * @private
   */
  _editHotspot(id) {
    this.activeHotspotId = id;
    const hotspot = this.data.hotspots.find(h => h.id === id);

    // Remove existing editor if any
    if (this.nodes.hotspotEditor) {
      this.nodes.hotspotEditor.remove();
    }

    // Create editor
    this.nodes.hotspotEditor = this._make('div', [this.CSS.hotspotEditor]);

    // Header
    const header = this._make('div', [this.CSS.hotspotEditorHeader]);
    const titleInput = this._make('input', [this.CSS.input, this.CSS.hotspotEditorTitle], {
      value: hotspot.title,
      placeholder: 'Hotspot Title',
      type: 'text',
    });
    header.appendChild(titleInput);
    this.nodes.hotspotEditor.appendChild(header);

    // Content
    const contentTextarea = this._make('textarea', [this.CSS.textarea, this.CSS.hotspotEditorContent], {
      value: hotspot.content,
      placeholder: 'Hotspot Content',
      rows: 5,
    });
    this.nodes.hotspotEditor.appendChild(contentTextarea);

    // Actions
    const actions = this._make('div', [this.CSS.hotspotEditorActions]);

    const saveButton = this._make('button', [this.CSS.button, this.CSS.buttonPrimary], {
      textContent: this.settings.buttonLabels.save,
      type: 'button',
    });

    const cancelButton = this._make('button', [this.CSS.button], {
      textContent: this.settings.buttonLabels.cancel,
      type: 'button',
    });

    const removeButton = this._make('button', [this.CSS.button, this.CSS.buttonDanger], {
      textContent: this.settings.buttonLabels.remove,
      type: 'button',
    });

    actions.appendChild(saveButton);
    actions.appendChild(cancelButton);
    actions.appendChild(removeButton);
    this.nodes.hotspotEditor.appendChild(actions);

    // Add to DOM
    this.nodes.wrapper.appendChild(this.nodes.hotspotEditor);

    // Event handlers
    saveButton.addEventListener('click', () => {
      hotspot.title = titleInput.value;
      hotspot.content = contentTextarea.value;
      this.nodes.hotspotEditor.remove();
      this.activeHotspotId = null;
    });

    cancelButton.addEventListener('click', () => {
      this.nodes.hotspotEditor.remove();
      this.activeHotspotId = null;
    });

    removeButton.addEventListener('click', () => {
      this._removeHotspot(id);
      this.nodes.hotspotEditor.remove();
      this.activeHotspotId = null;
    });
  }

  /**
   * Removes a hotspot
   * @param {string} id - hotspot id
   * @private
   */
  _removeHotspot(id) {
    // Remove from DOM
    const hotspotElement = this.nodes.imageContainer.querySelector(`[data-id="${id}"]`);
    if (hotspotElement) {
      hotspotElement.remove();
    }

    // Remove from data
    this.data.hotspots = this.data.hotspots.filter(h => h.id !== id);
  }

  /**
   * Generates a unique ID for a hotspot
   * @returns {string}
   * @private
   */
  _generateId() {
    return Math.random().toString(36).substring(2, 11);
  }

  /**
   * Helper for making Elements
   * @param {string} tagName - name of the tag
   * @param {string[]} classNames - list of CSS classes
   * @param {object} attributes - object with attributes
   * @returns {HTMLElement}
   * @private
   */
  _make(tagName, classNames = [], attributes = {}) {
    const el = document.createElement(tagName);

    if (Array.isArray(classNames)) {
      el.classList.add(...classNames);
    } else if (classNames) {
      el.classList.add(classNames);
    }

    for (const attrName in attributes) {
      el[attrName] = attributes[attrName];
    }

    return el;
  }

  /**
   * Return Tool data
   * @returns {object}
   */
  save() {
    return {
      image: this.data.image,
      hotspots: this.data.hotspots,
      caption: this.nodes.caption ? this.nodes.caption.innerHTML : this.data.caption,
    };
  }

  /**
   * Sanitizer rules
   */
  static get sanitize() {
    return {
      image: {
        url: true,
        alt: true,
      },
      hotspots: {
        id: true,
        x: true,
        y: true,
        title: true,
        content: true,
      },
      caption: {
        br: true,
        a: {
          href: true,
          target: true,
          rel: true,
        },
        b: {},
        i: {},
        strong: {},
        em: {},
      },
    };
  }

  /**
   * Tool's CSS properties
   * @returns {object}
   */
  static get CSS() {
    return {
      wrapper: 'image-hotspot-tool',
    };
  }

  /**
   * Specify Block Tool's title and icon
   * @returns {object}
   */
  static get toolbox() {
    return {
      title: 'Image Hotspots',
      icon: '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 5H4V19L20 19V5ZM4 3C2.89543 3 2 3.89543 2 5V19C2 20.1046 2.89543 21 4 21H20C21.1046 21 22 20.1046 22 19V5C22 3.89543 21.1046 3 20 3H4Z"/><circle cx="8" cy="12" r="2" /><circle cx="16" cy="8" r="2" /><circle cx="16" cy="16" r="2" /></svg>',
    };
  }
}
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
                    class: ImageHotspot,
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