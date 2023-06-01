import { ElMessage } from 'element-plus';

type popupType = 'error' | 'info' | 'success' | 'warning';

function popup(type: popupType, message: string, httpCode?: number): void {
  if (httpCode) {
    message = `HTTP ${httpCode}: ${message}`;
  }

  ElMessage({
    message,
    type,
    duration: 15000,
    showClose: true,
    grouping: true,
  });
}

export { popup };
