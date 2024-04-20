import type { FormItemRule, FormRules } from 'element-plus';
import { reactive } from 'vue';

const invalidFieldMessage = 'Field is not valid';
const emptyMessage = 'Field must not be empty';
const lengthTooSmallMessage = 'Value is too short';

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function validateTextMinLength(rule: any, value: any, callback: any) {
  if (!value) {
    callback();
    return;
  }

  if (value.length < 3) {
    callback(new Error(lengthTooSmallMessage));
    return;
  }

  callback();
}

const textMinRule = [
  {
    validator: validateTextMinLength,
    trigger: ['blur', 'change'],
  },
];

const requiredRule: FormItemRule[] = [
  {
    required: true,
    message: emptyMessage,
    trigger: ['blur', 'change'],
  },
];

const textRequiredMinRule: FormItemRule[] = [...requiredRule, ...textMinRule];

const emailRule: FormItemRule[] = [
  {
    type: 'email',
    message: invalidFieldMessage,
    trigger: ['blur', 'change'],
  },
];

const emailRequiredRule: FormItemRule[] = [...emailRule, ...requiredRule];

const urlRule: FormItemRule[] = [
  {
    type: 'url',
    message: invalidFieldMessage,
    trigger: ['blur', 'change'],
  },
];

const urlRequiredRule: FormItemRule[] = [...urlRule, ...requiredRule];

export const bookRules = reactive<FormRules>({
  name: textRequiredMinRule,
  description: textMinRule,
  pageCount: requiredRule,
  datePublished: requiredRule,
  publisher: requiredRule,
  tags: requiredRule,
  authors: requiredRule,
});

export const publisherRules = reactive<FormRules>({
  name: textRequiredMinRule,
  email: emailRequiredRule,
  address: requiredRule,
  website: urlRequiredRule,
});

export const authorRules = reactive<FormRules>({
  firstName: textRequiredMinRule,
  lastName: textRequiredMinRule,
  middleName: textMinRule,
  email: emailRule,
  website: urlRule,
});

export const userRoles = reactive<FormRules>({
  firstName: textRequiredMinRule,
  lastName: textRequiredMinRule,
  middleName: textMinRule,
  email: emailRequiredRule,
  roles: requiredRule,
});

export const tagRules = reactive<FormRules>({
  name: textRequiredMinRule,
});

export const loginRules = reactive<FormRules>({
  email: emailRequiredRule,
  password: requiredRule,
});

export const requestPasswordResetRules = reactive<FormRules>({
  email: emailRequiredRule,
});

export const orderRules = reactive<FormRules>({
  firstName: textRequiredMinRule,
  lastName: textRequiredMinRule,
  middleName: textMinRule,
  phoneNumber: requiredRule,
  quantity: requiredRule,
});
