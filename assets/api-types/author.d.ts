interface BookAuthor {
  id: number;
  fullName: string;
  firstName: string;
  lastName: string;
  middleName?: string;
  website?: string;
  email?: string;
  slug: string;
  isDeleted: string;
  books?: Book[]
}
