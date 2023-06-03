import type { UploadRawFile } from 'element-plus';

type UserRole = 'ROLE_ADMIN' | 'ROLE_USER';

interface UploadUserProfile {
  id?: number;
  firstName: string;
  lastName: string;
  middleName?: string;
  email: string;
  roles: UserRole[];
  image?: UploadRawFile;
}

interface UserProfile extends UploadUserProfile {
  id: number;
  fullName: string;
  image?: string;
  slug: string;
  isDeleted: boolean;
  isActive: boolean;
}

interface UploadBookAuthor {
  id?: number;
  firstName: string;
  lastName: string;
  middleName?: string;
  website?: string;
  email?: string;
  image?: UploadRawFile;
}

interface BookAuthor extends UploadBookAuthor {
  id: number;
  fullName: string;
  image?: string;
  slug: string;
  isDeleted: string;
  books?: BookShort[];
}

interface UploadBookPublisher {
  id?: number;
  name: string;
  address: string;
  email: string;
  website: string;
  image?: UploadRawFile;
}

interface BookPublisher extends UploadBookPublisher {
  id: number;
  image?: string;
  slug: string;
  isDeleted: string;
}

interface UploadBookTag {
  id?: number;
  name: string;
}

interface BookTag extends UploadBookTag {
  id: number;
  slug: string;
  isDeleted: boolean;
}

interface UploadBook {
  id?: number;
  name: string;
  image?: UploadRawFile;
  tags: number[];
  authors: number[];
  publisher: number;
  datePublished: string;
  pageCount: number;
  description?: string;
}

interface BookShort extends UploadBook {
  id: number;
  image?: string;
  isDeleted: boolean;
  tags: Tag[];
  authors: undefined;
  publisher: undefined;
  datePublished: undefined;
  pageCount: undefined;
  description?: undefined;
}

interface BookFull extends BookShort {
  authors: BookAuthor[];
  publisher: BookPublisher;
  datePublished: Date;
  pageCount: number;
  description?: string;
}

interface ApiMeta {
  paginated: boolean;
  pageSize: number;
  offset: number;
  totalCount: number;
}

interface ApiCollection<ApiType> {
  meta: ApiMeta;
  data: ApiType[];
}
