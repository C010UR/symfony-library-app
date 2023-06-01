type UserRole = 'ROLE_ADMIN' | 'ROLE_USER';

interface UserProfile {
  email: string;
  image?: string;
  roles: UserRole[];
  is_deleted?: boolean;
  is_active?: boolean;
}
