# API DEVELOPMENT STANDARDS
## SIKADES API Gateway - Best Practices & Conventions

---

**Version:** 1.0  
**Last Updated:** 26 Desember 2024  
**Target:** NestJS, TypeScript, Team Collaboration  

---

## 📋 TABLE OF CONTENTS

1. [Code Style & Formatting](#1-code-style--formatting)
2. [Naming Conventions](#2-naming-conventions)
3. [File Organization](#3-file-organization)
4. [Git Workflow](#4-git-workflow)
5. [TypeScript Best Practices](#5-typescript-best-practices)
6. [API Design Principles](#6-api-design-principles)
7. [Error Handling](#7-error-handling)
8. [Security Guidelines](#8-security-guidelines)
9. [Testing Standards](#9-testing-standards)
10. [Documentation](#10-documentation)
11. [Performance Optimization](#11-performance-optimization)
12. [Code Review Checklist](#12-code-review-checklist)

---

## 1. CODE STYLE & FORMATTING

### 1.1 ESLint Configuration

```json
// .eslintrc.json
{
  "parser": "@typescript-eslint/parser",
  "parserOptions": {
    "project": "tsconfig.json",
    "sourceType": "module"
  },
  "plugins": ["@typescript-eslint/eslint-plugin"],
  "extends": [
    "plugin:@typescript-eslint/recommended",
    "plugin:prettier/recommended"
  ],
  "root": true,
  "env": {
    "node": true,
    "jest": true
  },
  "ignorePatterns": [".eslintrc.js"],
  "rules": {
    "@typescript-eslint/interface-name-prefix": "off",
    "@typescript-eslint/explicit-function-return-type": "off",
    "@typescript-eslint/explicit-module-boundary-types": "off",
    "@typescript-eslint/no-explicit-any": "warn",
    "@typescript-eslint/no-unused-vars": ["error", { "argsIgnorePattern": "^_" }],
    "no-console": ["warn", { "allow": ["warn", "error"] }]
  }
}
```

### 1.2 Prettier Configuration

```json
// .prettierrc
{
  "singleQuote": true,
  "trailingComma": "all",
  "tabWidth": 2,
  "semi": true,
  "printWidth": 100,
  "arrowParens": "always"
}
```

### 1.3 EditorConfig

```ini
# .editorconfig
root = true

[*]
charset = utf-8
indent_style = space
indent_size = 2
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true

[*.md]
trim_trailing_whitespace = false
```

### 1.4 Pre-commit Hooks (Husky)

```bash
npm install -D husky lint-staged

npx husky install
npx husky add .husky/pre-commit "npx lint-staged"
```

```json
// package.json
{
  "lint-staged": {
    "*.ts": [
      "eslint --fix",
      "prettier --write"
    ]
  }
}
```

---

## 2. NAMING CONVENTIONS

### 2.1 Files & Folders

```
✅ GOOD:
- user.entity.ts
- users.controller.ts
- users.service.ts
- create-user.dto.ts
- auth.module.ts

❌ BAD:
- User.ts
- userController.ts
- CreateUserDTO.ts
```

**Rules:**
- Use kebab-case for file names
- Use descriptive suffixes (`.controller`, `.service`, `.module`, `.dto`, `.entity`)
- Folder names: lowercase, kebab-case

### 2.2 Classes

```typescript
✅ GOOD:
export class UsersController {}
export class DashboardDesaService {}
export class CreateUserDto {}
export class User {} // Entity

❌ BAD:
export class usersController {}
export class dashboard_service {}
export class createUserDto {}
```

**Rules:**
- Use PascalCase
- Descriptive names with suffix (Controller, Service, Module, etc.)

### 2.3 Interfaces & Types

```typescript
✅ GOOD:
interface ApiResponse<T> {
  success: boolean;
  data: T;
}

type UserRole = 'admin' | 'kepala_desa' | 'camat';

❌ BAD:
interface IApiResponse {} // Don't prefix with I
type userRole = string; // Too generic
```

**Rules:**
- No `I` prefix for interfaces (TypeScript convention)
- Use descriptive names
- Prefer `interface` over `type` when possible

### 2.4 Variables & Functions

```typescript
✅ GOOD:
const userCount = 10;
const isActive = true;
const getUserById = (id: number) => {};

function calculateRealisasi(anggaran: number, realisasi: number): number {
  return (realisasi / anggaran) * 100;
}

❌ BAD:
const UserCount = 10; // PascalCase reserved for classes
const user_count = 10; // Use camelCase
const active = true; // Not descriptive
```

**Rules:**
- Use camelCase
- Boolean variables: prefix with `is`, `has`, `should`
- Functions: verb + noun pattern

### 2.5 Constants

```typescript
✅ GOOD:
const API_VERSION = 'v1';
const MAX_RETRY_ATTEMPTS = 3;
const DEFAULT_PAGE_SIZE = 50;

❌ BAD:
const apiVersion = 'v1';
const max_retry = 3;
```

**Rules:**
- Use SCREAMING_SNAKE_CASE for true constants
- Group related constants in enums or objects

### 2.6 Database Tables & Columns

```typescript
✅ GOOD:
@Entity('users')
export class User {
  @Column({ name: 'kode_desa' })
  kodeDesa: string; // camelCase in code
  
  @Column({ name: 'created_at' })
  createdAt: Date;
}

❌ BAD:
@Entity('Users')
export class User {
  @Column()
  kode_desa: string; // snake_case in code
}
```

**Rules:**
- Table names: lowercase, plural (users, transactions)
- Column names in DB: snake_case (kode_desa, created_at)
- Property names in code: camelCase (kodeDesa, createdAt)

---

## 3. FILE ORGANIZATION

### 3.1 Module Structure

```
users/
├── dto/
│   ├── create-user.dto.ts
│   ├── update-user.dto.ts
│   └── user-response.dto.ts
├── entities/
│   └── user.entity.ts
├── interfaces/
│   └── user.interface.ts
├── users.controller.ts
├── users.controller.spec.ts
├── users.service.ts
├── users.service.spec.ts
└── users.module.ts
```

### 3.2 Import Order

```typescript
// 1. Node.js built-in modules
import { readFileSync } from 'fs';

// 2. External dependencies
import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';

// 3. Internal modules (absolute imports)
import { User } from '@/users/entities/user.entity';
import { AuthService } from '@/auth/auth.service';

// 4. Relative imports
import { CreateUserDto } from './dto/create-user.dto';
import { UserResponseDto } from './dto/user-response.dto';
```

**Use Path Aliases:**
```json
// tsconfig.json
{
  "compilerOptions": {
    "baseUrl": "./",
    "paths": {
      "@/*": ["src/*"],
      "@common/*": ["src/common/*"],
      "@config/*": ["src/config/*"]
    }
  }
}
```

---

## 4. GIT WORKFLOW

### 4.1 Branch Strategy

```
main (production)
  └── develop (staging)
       ├── feature/auth-module
       ├── feature/dashboard-desa
       ├── bugfix/login-validation
       └── hotfix/critical-security
```

**Branch Naming:**
- `feature/feature-name` - New features
- `bugfix/bug-description` - Bug fixes
- `hotfix/critical-issue` - Production hotfixes
- `refactor/what-changed` - Code refactoring
- `docs/what-updated` - Documentation updates

### 4.2 Commit Messages (Conventional Commits)

```bash
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation
- **style**: Formatting (no code change)
- **refactor**: Code restructuring
- **perf**: Performance improvement
- **test**: Adding tests
- **chore**: Build tasks, dependencies

**Examples:**
```bash
✅ GOOD:
feat(auth): implement JWT refresh token
fix(dashboard): correct realisasi calculation
docs(api): update swagger annotations
refactor(users): extract validation to separate service
perf(query): optimize aggregation with indexes

❌ BAD:
Fixed bug
Updated code
Changes
WIP
```

### 4.3 Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Feature
- [ ] Bug fix
- [ ] Refactor
- [ ] Documentation
- [ ] Performance improvement

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] Tests added/updated
- [ ] All tests passing
- [ ] No new warnings
- [ ] Dependent changes merged

## Testing
How was this tested?

## Screenshots (if applicable)
```

### 4.4 Git Commands Best Practices

```bash
# Always pull before pushing
git pull origin develop
git push origin feature/my-feature

# Use rebase for clean history (on feature branches)
git rebase develop

# Squash commits before merging
git rebase -i HEAD~5

# Never force push to main/develop
git push --force # ❌ NEVER on shared branches

# Write meaningful commit messages
git commit -m "feat(auth): add password reset endpoint

- Implement forgot password flow
- Send email with reset token
- Validate token expiry (1 hour)
- Add unit tests for token generation"
```

---

## 5. TYPESCRIPT BEST PRACTICES

### 5.1 Type Annotations

```typescript
✅ GOOD:
function calculatePercentage(value: number, total: number): number {
  return (value / total) * 100;
}

interface DashboardData {
  keuangan: KeuanganStats;
  demografi: DemografiStats;
}

const get UserById = async (id: number): Promise<User> => {
  return await userRepository.findOne({ where: { id } });
};

❌ BAD:
function calculate(a, b) { // No types
  return (a / b) * 100;
}

const getData = async (id) => { // No param type, no return type
  return await repo.find(id);
};
```

### 5.2 Avoid `any`

```typescript
✅ GOOD:
interface ApiResponse<T> {
  success: boolean;
  data: T;
}

function processData<T>(data: T): ApiResponse<T> {
  return {
    success: true,
    data,
  };
}

❌ BAD:
function processData(data: any): any {
  return {
    success: true,
    data,
  };
}
```

**If you MUST use `any`, add comment explaining why:**
```typescript
// @ts-ignore: Third-party library lacks type definitions
const result: any = externalLib.doSomething();
```

### 5.3 Use Enums for Constants

```typescript
✅ GOOD:
enum UserRole {
  ADMIN = 'admin',
  KEPALA_DESA = 'kepala_desa',
  CAMAT = 'camat',
}

enum HttpStatus {
  OK = 200,
  CREATED = 201,
  BAD_REQUEST = 400,
  UNAUTHORIZED = 401,
}

❌ BAD:
const USER_ROLES = {
  ADMIN: 'admin',
  KEPALA_DESA: 'kepala_desa',
};
```

### 5.4 Optional Chaining & Nullish Coalescing

```typescript
✅ GOOD:
const userName = user?.profile?.name ?? 'Unknown';
const email = user?.email || 'no-email@example.com';

❌ BAD:
const userName = user && user.profile && user.profile.name ? user.profile.name : 'Unknown';
```

---

## 6. API DESIGN PRINCIPLES

### 6.1 RESTful Endpoints

```typescript
✅ GOOD:
GET    /api/v1/users           // List users
GET    /api/v1/users/:id       // Get user by ID
POST   /api/v1/users           // Create user
PUT    /api/v1/users/:id       // Update user (full)
PATCH  /api/v1/users/:id       // Update user (partial)
DELETE /api/v1/users/:id       // Delete user

POST   /api/v1/auth/login      // Login (action)
POST   /api/v1/auth/logout     // Logout (action)

❌ BAD:
GET    /api/v1/getUsers
POST   /api/v1/createUser
GET    /api/v1/user/delete/:id  // Should be DELETE
POST   /api/v1/users/:id/update // Should be PUT/PATCH
```

### 6.2 Request/Response Format

**Request:**
```typescript
// POST /api/v1/users
{
  "username": "johndoe",
  "email": "john@example.com",
  "role": "kepala_desa"
}
```

**Success Response:**
```typescript
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "role": "kepala_desa",
    "createdAt": "2024-12-26T10:00:00Z"
  }
}
```

**Error Response:**
```typescript
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "code": "VALIDATION_ERROR",
    "validation": {
      "email": ["Email format is invalid"],
      "password": ["Password must be at least 8 characters"]
    }
  },
  "timestamp": "2024-12-26T10:00:00Z"
}
```

### 6.3 Pagination

```typescript
// Request
GET /api/v1/users?page=2&limit=50&sort=-createdAt

// Response
{
  "success": true,
  "data": {
    "items": [...],
    "meta": {
      "currentPage": 2,
      "perPage": 50,
      "total": 523,
      "totalPages": 11,
      "hasNext": true,
      "hasPrev": true
    }
  }
}
```

### 6.4 Filtering & Sorting

```typescript
// Multiple filters
GET /api/v1/users?role=kepala_desa&isActive=true&level=desa

// Sorting (prefix with - for DESC)
GET /api/v1/users?sort=-createdAt,username

// Date range
GET /api/v1/transactions?startDate=2024-01-01&endDate=2024-12-31

// Search
GET /api/v1/users?search=john
```

---

## 7. ERROR HANDLING

### 7.1 Custom Exceptions

```typescript
// src/common/exceptions/business.exception.ts
import { HttpException, HttpStatus } from '@nestjs/common';

export class BusinessException extends HttpException {
  constructor(message: string, code: string) {
    super(
      {
        success: false,
        message,
        errors: {
          code,
        },
      },
      HttpStatus.BAD_REQUEST,
    );
  }
}

// Usage
if (user.realisasi < 0) {
  throw new BusinessException(
    'Realisasi tidak boleh negatif',
    'INVALID_REALISASI',
  );
}
```

### 7.2 Error Codes

```typescript
enum ErrorCode {
  // Authentication
  INVALID_CREDENTIALS = 'INVALID_CREDENTIALS',
  TOKEN_EXPIRED = 'TOKEN_EXPIRED',
  INSUFFICIENT_PERMISSION = 'INSUFFICIENT_PERMISSION',
  
  // Validation
  VALIDATION_ERROR = 'VALIDATION_ERROR',
  INVALID_INPUT = 'INVALID_INPUT',
  
  // Business Logic
  RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND',
  DUPLICATE_RESOURCE = 'DUPLICATE_RESOURCE',
  INVALID_STATE = 'INVALID_STATE',
  
  // System
  DATABASE_ERROR = 'DATABASE_ERROR',
  EXTERNAL_API_ERROR = 'EXTERNAL_API_ERROR',
}
```

### 7.3 Try-Catch Pattern

```typescript
✅ GOOD:
async getUserById(id: number): Promise<User> {
  try {
    const user = await this.userRepository.findOne({ where: { id } });
    
    if (!user) {
      throw new NotFoundException(`User with ID ${id} not found`);
    }
    
    return user;
  } catch (error) {
    if (error instanceof NotFoundException) {
      throw error; // Re-throw known errors
    }
    
    // Log unexpected errors
    this.logger.error(`Failed to get user ${id}:`, error);
    throw new InternalServerErrorException('Failed to retrieve user');
  }
}

❌ BAD:
async getUserById(id: number) {
  const user = await this.userRepository.findOne({ where: { id } });
  return user; // No error handling
}
```

---

## 8. SECURITY GUIDELINES

### 8.1 Input Validation

```typescript
✅ GOOD:
// create-user.dto.ts
import { IsString, IsEmail, MinLength, MaxLength, Matches } from 'class-validator';

export class CreateUserDto {
  @IsString()
  @MinLength(3)
  @MaxLength(50)
  @Matches(/^[a-zA-Z0-9_]+$/, {
    message: 'Username can only contain letters, numbers and underscores',
  })
  username: string;

  @IsEmail()
  email: string;

  @IsString()
  @MinLength(8)
  @Matches(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/, {
    message: 'Password must contain uppercase, lowercase and number',
  })
  password: string;
}
```

### 8.2 SQL Injection Prevention

```typescript
✅ GOOD:
// Use parameterized queries
const user = await this.userRepository.findOne({
  where: { username },
});

// Use query builder
const users = await this.userRepository
  .createQueryBuilder('user')
  .where('user.role = :role', { role })
  .getMany();

❌ BAD:
// Never concatenate user input
const query = `SELECT * FROM users WHERE username = '${username}'`;
await this.db.query(query);
```

### 8.3 Sensitive Data

```typescript
✅ GOOD:
// Exclude password from API response
@Exclude()
password: string;

// Or use class-transformer
@UseInterceptors(ClassSerializerInterceptor)
@Get('profile')
getProfile(@CurrentUser() user: User) {
  return user; // password automatically excluded
}

❌ BAD:
@Get('users/:id')
async getUser(@Param('id') id: number) {
  return await this.userRepository.findOne({ where: { id } });
  // Returns password field!
}
```

### 8.4 Rate Limiting

```typescript
// Use @nestjs/throttler
import { ThrottlerGuard } from '@nestjs/throttler';

@UseGuards(ThrottlerGuard)
@Controller('auth')
export class AuthController {
  @Throttle(5, 60) // 5 requests per 60 seconds
  @Post('login')
  async login(@Body() loginDto: LoginDto) {
    // ...
  }
}
```

### 8.5 CORS Configuration

```typescript
// main.ts
app.enableCors({
  origin: process.env.CORS_ORIGIN?.split(',') || 'http://localhost:3000',
  methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
  credentials: true,
  maxAge: 3600,
});
```

---

## 9. TESTING STANDARDS

### 9.1 Test Coverage Requirements

- **Minimum Coverage**: 80%
- **Controllers**: 90%+ (critical paths)
- **Services**: 85%+ (business logic)
- **Utils**: 95%+ (pure functions)

### 9.2 Unit Test Structure

```typescript
describe('UsersService', () => {
  let service: UsersService;
  let repository: Repository<User>;

  beforeEach(async () => {
    // Setup
  });

  describe('findById', () => {
    it('should return a user when found', async () => {
      // Arrange
      const userId = 1;
      const expectedUser = { id: 1, username: 'test' };
      jest.spyOn(repository, 'findOne').mockResolvedValue(expectedUser as User);

      // Act
      const result = await service.findById(userId);

      // Assert
      expect(result).toEqual(expectedUser);
      expect(repository.findOne).toHaveBeenCalledWith({ where: { id: userId } });
    });

    it('should throw NotFoundException when user not found', async () => {
      // Arrange
      jest.spyOn(repository, 'findOne').mockResolvedValue(null);

      // Act & Assert
      await expect(service.findById(999)).rejects.toThrow(NotFoundException);
    });
  });
});
```

### 9.3 E2E Test Example

```typescript
// test/auth.e2e-spec.ts
describe('AuthController (e2e)', () => {
  let app: INestApplication;

  beforeAll(async () => {
    const moduleFixture = await Test.createTestingModule({
      imports: [AppModule],
    }).compile();

    app = moduleFixture.createNestApplication();
    await app.init();
  });

  describe('/auth/login (POST)', () => {
    it('should login successfully', () => {
      return request(app.getHttpServer())
        .post('/auth/login')
        .send({
          username: 'test',
          password: 'password123',
        })
        .expect(200)
        .expect((res) => {
          expect(res.body.success).toBe(true);
          expect(res.body.data).toHaveProperty('accessToken');
        });
    });

    it('should fail with invalid credentials', () => {
      return request(app.getHttpServer())
        .post('/auth/login')
        .send({
          username: 'test',
          password: 'wrong',
        })
        .expect(401);
    });
  });
});
```

### 9.4 Test Naming

```typescript
✅ GOOD:
it('should return user when valid ID is provided')
it('should throw NotFoundException when user does not exist')
it('should hash password before saving to database')

❌ BAD:
it('test user')
it('works')
it('returns data')
```

---

## 10. DOCUMENTATION

### 10.1 Code Comments

```typescript
✅ GOOD:
/**
 * Calculate realisasi percentage based on anggaran and actual spending
 * @param anggaran - Total budget allocated
 * @param realisasi - Actual spending
 * @returns Percentage (0-100) with 2 decimal places
 * @throws BusinessException if anggaran is zero
 */
function calculateRealisasiPersen(anggaran: number, realisasi: number): number {
  if (anggaran === 0) {
    throw new BusinessException('Anggaran cannot be zero', 'INVALID_ANGGARAN');
  }
  
  return Math.round((realisasi / anggaran) * 100 * 100) / 100;
}

// Complex algorithm needs explanation
// Using Haversine formula to calculate distance between two GPS coordinates
const distance = calculateDistance(lat1, lng1, lat2, lng2);

❌ BAD:
// Get user
function getUser() {}

// Calculate
const result = (a / b) * 100; // What is being calculated?
```

### 10.2 Swagger/OpenAPI Annotations

```typescript
@ApiTags('Dashboard Desa')
@Controller('dashboard/desa')
export class DashboardDesaController {
  @Get(':kodeDesa')
  @ApiOperation({ summary: 'Get dashboard overview for a desa' })
  @ApiParam({ name: 'kodeDesa', description: 'Kode wilayah desa (13 digits)' })
  @ApiResponse({
    status: 200,
    description: 'Dashboard data retrieved successfully',
    type: DashboardDesaResponseDto,
  })
  @ApiResponse({ status: 404, description: 'Desa not found' })
  @ApiBearerAuth()
  async getDashboard(@Param('kodeDesa') kodeDesa: string) {
    return await this.service.getDashboard(kodeDesa);
  }
}
```

### 10.3 README Templates

```markdown
# Module Name

## Description
Brief description of what this module does

## Features
- Feature 1
- Feature 2

## API Endpoints

### GET /endpoint
Description

**Parameters:**
- `param1` (required): Description
- `param2` (optional): Description

**Response:**
```json
{
  "success": true,
  "data": {}
}
```

## Dependencies
- dependency1
- dependency2

## Testing
```bash
npm run test:unit
```
```

---

## 11. PERFORMANCE OPTIMIZATION

### 11.1 Database Queries

```typescript
✅ GOOD:
// Use select to limit fields
const users = await this.userRepository.find({
  select: ['id', 'username', 'email'],
  where: { isActive: true },
});

// Use pagination
const [users, total] = await this.userRepository.findAndCount({
  skip: (page - 1) * limit,
  take: limit,
});

// Use indexes
@Index(['kodeDesa', 'tahun'])
@Entity('apbdes')
export class Apbdes {}

❌ BAD:
// Fetching all columns and all rows
const users = await this.userRepository.find();

// N+1 query problem
for (const user of users) {
  user.profile = await this.profileRepository.findOne({ where: { userId: user.id } });
}
```

### 11.2 Caching

```typescript
// Install cache manager
npm install cache-manager

// Use caching
@Injectable()
export class DashboardService {
  constructor(
    @Inject(CACHE_MANAGER)
    private cacheManager: Cache,
  ) {}

  async getDashboard(kodeDesa: string) {
    const cacheKey = `dashboard:${kodeDesa}`;
    
    // Try cache first
    const cached = await this.cacheManager.get(cacheKey);
    if (cached) {
      return cached;
    }

    // Fetch from database
    const data = await this.fetchDashboardData(kodeDesa);
    
    // Cache for 5 minutes
    await this.cacheManager.set(cacheKey, data, { ttl: 300 });
    
    return data;
  }
}
```

### 11.3 Async Operations

```typescript
✅ GOOD:
// Parallel execution
const [users, orders, stats] = await Promise.all([
  this.userRepository.find(),
  this.orderRepository.find(),
  this.getStats(),
]);

❌ BAD:
// Sequential execution (slow!)
const users = await this.userRepository.find();
const orders = await this.orderRepository.find();
const stats = await this.getStats();
```

---

## 12. CODE REVIEW CHECKLIST

### 12.1 Before Submitting PR

- [ ] Code follows style guide
- [ ] All tests pass locally
- [ ] New tests added for new functionality
- [ ] Documentation updated
- [ ] No `console.log` in code
- [ ] No commented-out code
- [ ] Environment variables externalized
- [ ] Error handling implemented
- [ ] Input validation added
- [ ] Security considerations addressed
- [ ] Performance optimized
- [ ] Swagger annotations added

### 12.2 Reviewer Checklist

- [ ] Code is readable and maintainable
- [ ] Logic is correct
- [ ] Tests are comprehensive
- [ ] No code duplication
- [ ] Naming is clear and consistent
- [ ] Error handling is appropriate
- [ ] Security vulnerabilities checked
- [ ] Performance impact considered
- [ ] Documentation is clear
- [ ] No breaking changes (or documented)

### 12.3 Common Issues to Look For

**Security:**
- Exposed secrets in code
- SQL injection vulnerabilities
- Missing input validation
- Exposed sensitive data in logs

**Performance:**
- N+1 query problems
- Missing database indexes
- Large payloads
- Memory leaks

**Code Quality:**
- Overly complex functions (>50 lines)
- Deep nesting (>3 levels)
- God classes (>500 lines)
- Tight coupling

---

## APPENDIX: Quick Reference

### Useful VSCode Shortcuts

```
Ctrl/Cmd + Shift + P  → Command palette
Ctrl/Cmd + P          → Quick file open
Ctrl/Cmd + /          → Toggle comment
Shift + Alt + F       → Format document
F2                    → Rename symbol
F12                   → Go to definition
Ctrl/Cmd + Click      → Go to definition
```

### NPM Scripts

```bash
npm run start:dev     # Development mode
npm run build         # Build for production
npm run test          # Run unit tests
npm run test:cov      # Test with coverage
npm run test:e2e      # E2E tests
npm run lint          # Lint code
npm run format        # Format code
```

---

**End of Development Standards**

**Maintenance:**
- Review and update every 6 months
- Add new patterns as they emerge
- Remove outdated practices

**Questions or Suggestions:**
Create an issue in the repository or contact the team lead.

**Created by:** SIKADES Development Team  
**Last Updated:** 26 Desember 2024
