type UserRole = 'ROLE_ADMIN' | 'ROLE_USER';

interface UserProfile {
  id: number;
  fullName: string;
  firstName: string;
  lastName: string;
  middleName?: string;
  email: string;
  image?: string;
  roles: UserRole[];
  slug: string;
  isDeleted: boolean;
  isActive: boolean;
}

interface BookAuthor {
  id: number;
  fullName: string;
  firstName: string;
  lastName: string;
  middleName?: string;
  website?: string;
  email?: string;
  image?: string;
  slug: string;
  isDeleted: string;
  books?: BookShort[];
}

interface BookPublisher {
  id: number;
  name: string;
  address: string;
  email: string;
  website: string;
  image?: string;
  slug: string;
  isDeleted: string;
}

interface BookTag {
  id: number;
  name: string;
  slug: string;
  isDeleted: boolean;
}

interface BookShort {
  id: number;
  name: string;
  image?: string;
  isDeleted: boolean;
  tags: Tag[];
}

interface BookFull extends BookShort {
  authors: BookAuthor[];
  publisher: BookPublisher;
  datePublished: Date;
  pageCount: number;
  description?: string;
}

interface ApiCollection<ApiType> {
  meta: {
    paginated: boolean;
    pageSize: number;
    offset: number;
    totalCount: number;
  };
  data: ApiType[];
}

interface ApiFilterOption {
  name: string;
  label: string;
  type: string;
  operators: {
    [index: string]: string;
  };
  isOrderable: boolean;
  data?: {
    entity: string;
  };
}
