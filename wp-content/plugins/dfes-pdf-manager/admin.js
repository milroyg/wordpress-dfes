// jQuery(document).ready(function ($) {
//     $('.tam-upload-pdf').click(function (e) {
//         e.preventDefault();

//         // Properly reset mediaUploader instance for new posts
//         let mediaUploader = wp.media({
//             title: 'Select PDF',
//             library: { type: 'application/pdf' }, // Restrict selection to PDFs
//             button: { text: 'Use this PDF' },
//             multiple: false
//         });

//         // Ensure PDF filter is applied when the media library is opened
//         mediaUploader.on('open', function () {
//             var library = mediaUploader.state().get('library');
//             library.props.set('type', 'application/pdf'); // Ensure only PDFs are shown
//         });

//         // Handle file selection
//         mediaUploader.on('select', function () {
//             var attachment = mediaUploader.state().get('selection').first().toJSON();
//             var pdfUrl = attachment.url;

//             // Update the PDF URL field and preview link
//             $('#tam_pdf_link').val(pdfUrl);
//             $('#tam_pdf_preview').attr('href', pdfUrl).text('View PDF').show();

//             // Extract clean title from the PDF filename
//             var fileName = attachment.filename.replace(/\.pdf$/, '').replace(/[_-]/g, ' ').trim();

//             // Auto-fill English title field only if empty (so users can modify it)
//             var titleField = $('#tam_title_en');
//             if (!titleField.val().trim()) {
//                 titleField.val(fileName);
//             }
//         });

//         // Open the media uploader
//         mediaUploader.open();
//     });
// });
