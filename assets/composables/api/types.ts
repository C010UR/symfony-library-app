import type { UploadUserFile } from 'element-plus';

export type UserRole = 'ROLE_ADMIN' | 'ROLE_USER';

export interface UploadUserProfile {
  id?: number;
  firstName?: string;
  lastName?: string;
  middleName?: string;
  email?: string;
  roles: UserRole[];
  image?: UploadUserFile;
  removeImage?: boolean;
  url: string,
}

export interface UserProfile {
  id: number;
  slug: string;
  fullName: string;
  firstName: string;
  lastName: string;
  middleName?: string;
  email: string;
  roles: UserRole[];
  image?: string;
  isDeleted: boolean;
  isActive: boolean;
}

export interface UploadBookAuthor {
  id?: number;
  firstName?: string;
  lastName?: string;
  middleName?: string;
  website?: string;
  email?: string;
  image?: UploadUserFile;
  removeImage?: boolean;
}

export interface BookAuthor {
  id: number;
  slug: string;
  fullName: string;
  firstName: string;
  lastName: string;
  middleName: string;
  website: string;
  email: string;
  image?: string;
  isDeleted: string;
}

export interface UploadBookPublisher {
  id?: number;
  name?: string;
  address?: string;
  email?: string;
  website?: string;
  image?: UploadUserFile;
  removeImage?: boolean;
}

export interface BookPublisher {
  id: number;
  slug: string;
  image?: string;
  name: string;
  address: string;
  email: string;
  website: string;
  isDeleted: string;
}

export interface UploadBookTag {
  id?: number;
  name?: string;
  removeImage?: boolean;
}

export interface BookTag {
  id: number;
  slug: string;
  name: string;
  isDeleted: boolean;
}

export interface UploadBook {
  id?: number;
  name?: string;
  image?: UploadUserFile;
  tags: number[];
  authors: number[];
  publisher?: number;
  datePublished?: Date | string;
  pageCount?: number;
  description?: string;
  removeImage?: boolean;
}

export interface Book {
  id: number;
  name: string;
  image?: string;
  tags: BookTag[];
  slug: string;
  isDeleted: boolean;
  authors: BookAuthor[];
  publisher: BookPublisher;
  datePublished: string;
  pageCount: number;
  description?: string;
}

export interface UploadBookOrder {
  id?: number;
  firstName?: string;
  lastName?: string;
  middleName?: string;
  phoneNumber?: string;
  book?: number;
  quantity?: number;
}

export interface BookOrder {
  id: number;
  fullName: string;
  firstName: string;
  lastName: string;
  middleName: string;
  phoneNumber: string;
  book: Book;
  quantity: number;
  dateCreated: string;
  userCompleted?: UserProfile;
  dateCompleted?: string;
  isDeleted: boolean;
}

export interface ApiMeta {
  paginated: boolean;
  pageSize: number;
  offset: number;
  totalCount: number;
}

export interface ApiCollection<ApiType> {
  meta: ApiMeta;
  data: ApiType[];
}

export type FilterOperator =
  | 'eq'
  | 'gt'
  | 'lt'
  | 'gte'
  | 'lte'
  | 'neq'
  | 'null'
  | 'in'
  | 'not-in'
  | 'contains'
  | 'starts-with'
  | 'ends-with'
  | 'between';

export type FilterType =
  | 'boolean'
  | 'integer'
  | 'float'
  | 'string'
  | 'entity'
  | 'entities'
  | 'date'
  | 'array'
  | 'not_filterable';

export type FilterEntityType = 'book' | 'author' | 'tag' | 'publisher' | 'user';

export type OrderDirection = 'ASC' | 'DESC';

export interface Order {
  column: string;
  direction: OrderDirection;
}

export interface Filter {
  column?: string;
  operator?: FilterOperator;
  value?: unknown | [unknown, unknown];
}

export interface Pagination {
  pageSize: number;
  offset: number;
}

export interface ApiParams {
  [column: string]:
    | {
        [operator in FilterOperator]?: string;
      }
    | OrderDirection
    | boolean
    | number
    | undefined;
  order?: Record<string, OrderDirection>;
  pageSize?: number;
  offset?: number;
  deleted?: boolean;
}

interface BaseFilterOption {
  name: string;
  label: string;
  operators: Record<
    FilterOperator,
    {
      operator: FilterOperator;
      label: string;
    }
  >;
  isOrderable: boolean;
  isSearchable: boolean;
  data?: {
    null?: {
      true: string;
      false: string;
    };
  };
}

interface NormalFilterOption extends BaseFilterOption {
  type: 'integer' | 'float' | 'string' | 'date' | 'array' | 'none';
}

interface BooleanFilterOption extends BaseFilterOption {
  type: 'boolean';
  data: {
    bool: {
      true: string;
      false: string;
    };
    null?: {
      true: string;
      false: string;
    };
  };
}

interface EntityFilterOption extends BaseFilterOption {
  type: 'entity' | 'entities';
  data: {
    entity: FilterEntityType;
    null?: {
      true: string;
      false: string;
    };
  };
}

export type FilterOption = NormalFilterOption | BooleanFilterOption | EntityFilterOption;
