import { ElMessage } from 'element-plus';

type popupType = 'error' | 'info' | 'success' | 'warning';

function popup(type: popupType, message: string, httpCode?: number): void {
  const messages = message.split('<br>');

  messages.forEach(msg => {
    if (httpCode) {
      msg = `HTTP ${httpCode}: ${msg}`;
    }

    ElMessage({
      message: msg,
      type,
      duration: 7500,
      showClose: true,
      grouping: true,
    });
  });
}

export { popup };
