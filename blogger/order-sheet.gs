/**
 * Royal Vision Store: saves website orders into this Google Sheet.
 *
 * Setup (one time):
 * 1. Open the Google Sheet > Extensions > Apps Script, delete the sample code and paste this file.
 * 2. Deploy > New deployment > type "Web app".
 *    Execute as: Me.  Who has access: Anyone.  Deploy, then allow the permissions.
 * 3. Copy the Web app URL (https://script.google.com/macros/s/.../exec) and add it to the
 *    Blogger "Store Settings" gadget as:   sheet: <that URL>
 */

var SHEET_NAME = 'Orders';
var NOTIFY_EMAIL = true; // Email the sheet owner about every new order.
var HEADERS = ['Date', 'Order ID', 'Status', 'Name', 'Phone', 'City', 'Address', 'Items',
  'Subtotal', 'Delivery', 'Total', 'Payment', 'Note', 'Page'];

function doPost(e) {
  var p = (e && e.parameter) || {};
  if (p.website) return reply_({ ok: true }); // Spam bots fill the hidden field.
  if (!p.order_id || !p.phone || !p.items) return reply_({ ok: false, error: 'missing fields' });

  var lock = LockService.getScriptLock();
  lock.waitLock(10000);
  try {
    sheet_().appendRow([
      new Date(), text_(p.order_id, 40), 'New', text_(p.name, 80), "'" + clip_(p.phone, 20),
      text_(p.city, 60), text_(p.address, 300), text_(p.items, 3000),
      num_(p.subtotal), num_(p.delivery), num_(p.total),
      text_(p.payment, 40), text_(p.note, 300), text_(p.page, 300)
    ]);
  } finally {
    lock.releaseLock();
  }

  if (NOTIFY_EMAIL) {
    try {
      MailApp.sendEmail(Session.getEffectiveUser().getEmail(),
        'New order ' + clip_(p.order_id, 40) + ' - Rs ' + num_(p.total),
        [clip_(p.items, 3000), '', 'Total: Rs ' + num_(p.total), 'Payment: ' + clip_(p.payment, 40), '',
          'Name: ' + clip_(p.name, 80), 'Phone: ' + clip_(p.phone, 20), 'City: ' + clip_(p.city, 60),
          'Address: ' + clip_(p.address, 300), 'Note: ' + clip_(p.note, 300)].join('\n'));
    } catch (err) {
      // The order is already saved; a mail quota error should not lose it.
    }
  }
  return reply_({ ok: true });
}

function doGet() {
  return reply_({ ok: true, service: 'Royal Vision Store orders' });
}

function sheet_() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sh = ss.getSheetByName(SHEET_NAME) || ss.insertSheet(SHEET_NAME);
  if (sh.getLastRow() === 0) {
    sh.appendRow(HEADERS);
    sh.getRange(1, 1, 1, HEADERS.length).setFontWeight('bold');
    sh.setFrozenRows(1);
  }
  return sh;
}

function clip_(v, max) {
  return String(v == null ? '' : v).slice(0, max);
}

// Text that starts with = + - @ would run as a formula in the sheet, so it is stored as plain text.
function text_(v, max) {
  var s = clip_(v, max);
  return /^[=+\-@]/.test(s) ? "'" + s : s;
}

function num_(v) {
  var n = parseFloat(String(v == null ? '' : v).replace(/[^0-9.]/g, ''));
  return isNaN(n) ? 0 : n;
}

function reply_(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(ContentService.MimeType.JSON);
}
