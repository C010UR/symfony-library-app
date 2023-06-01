type UserRole = 'ROLE_ADMIN' | 'ROLE_USER';

interface UserProfile {
  id: number;
  email: string;
  image?: string;
  roles: UserRole[];
  isDeleted: boolean;
  isActive: boolean;
}
