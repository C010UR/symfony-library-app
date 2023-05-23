import { ElMessage } from 'element-plus';

function popup(type, message) {
  /* eslint-disable-next-line new-cap */
  ElMessage({
    message,
    type,
    duration: 15_000,
    showClose: true,
    grouping: true,
    dangerouslyUseHTMLString: true,
  });
}

export { popup };
