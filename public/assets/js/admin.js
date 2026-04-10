document.addEventListener('DOMContentLoaded', function () {

  // Upload area click-through
  document.querySelectorAll('.upload-area').forEach(function (area) {
    area.addEventListener('click', function () {
      var input = area.querySelector('input[type=file]');
      if (input) input.click();
    });
    var input = area.querySelector('input[type=file]');
    if (input) {
      input.addEventListener('change', function () {
        var label = area.querySelector('.upload-label');
        if (label && input.files[0]) {
          label.textContent = input.files[0].name;
        }
        area.closest('form').submit();
      });
    }
  });

  // Repo test connection
  var testBtn = document.getElementById('btn-test-repo');
  if (testBtn) {
    testBtn.addEventListener('click', function () {
      var url    = document.getElementById('repo-url').value.trim();
      var result = document.getElementById('test-result');
      var csrf   = document.querySelector('input[name="_csrf"]');
      if (!url) return;
      testBtn.disabled = true;
      testBtn.textContent = 'Testing…';
      result.className = 'test-result';
      result.textContent = '';

      var fd = new FormData();
      fd.append('url', url);
      if (csrf) fd.append('_csrf', csrf.value);

      fetch((document.body.dataset.base || '') + '/wc-admin/settings/repos/test', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          result.className = 'test-result ' + (data.ok ? 'ok' : 'err');
          result.textContent = data.message || data.error || 'Unknown response.';
        })
        .catch(function () {
          result.className = 'test-result err';
          result.textContent = 'Request failed.';
        })
        .finally(function () {
          testBtn.disabled = false;
          testBtn.textContent = 'Test connection';
        });
    });
  }

  // Confirm dangerous actions
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // Auto-dismiss flash after 4s
  document.querySelectorAll('.flash').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.4s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 4000);
  });

});
