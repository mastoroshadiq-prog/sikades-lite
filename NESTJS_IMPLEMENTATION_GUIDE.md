# NESTJS IMPLEMENTATION GUIDE
## SIKADES API Gateway - Complete Setup Tutorial

---

**Version:** 1.0  
**Last Updated:** 26 Desember 2024  
**Target:** NestJS v10.x, TypeScript, PostgreSQL  
**Estimated Setup Time:** 1-2 jam  

---

## 📋 TABLE OF CONTENTS

1. [Prerequisites](#1-prerequisites)
2. [Environment Setup](#2-environment-setup)
3. [Create New Project](#3-create-new-project)
4. [Project Structure](#4-project-structure)
5. [Database Setup](#5-database-setup)
6. [Authentication Module](#6-authentication-module)
7. [First API Endpoint](#7-first-api-endpoint)
8. [Validation & DTOs](#8-validation--dtos)
9. [Error Handling](#9-error-handling)
10. [Testing](#10-testing)
11. [Swagger Documentation](#11-swagger-documentation)
12. [Deployment](#12-deployment)

---

## 1. PREREQUISITES

### 1.1 Install Node.js

**Download & Install:**
- **Windows/Mac**: https://nodejs.org/en/download/
- **Recommended**: LTS version (v20.x)

**Verify Installation:**
```bash
node --version  # Should show v20.x.x
npm --version   # Should show v10.x.x
```

### 1.2 Install NestJS CLI

```bash
npm install -g @nestjs/cli
```

**Verify:**
```bash
nest --version  # Should show 10.x.x
```

### 1.3 Install IDE/Editor

**Recommended: Visual Studio Code**
- Download: https://code.visualstudio.com/

**VS Code Extensions (Install these!):**
1. ESLint
2. Prettier
3. TypeScript Import Sorter
4. REST Client
5. GitHub Copilot (optional, but helpful)

### 1.4 Install PostgreSQL

**Option 1: Local Install**
- Windows: https://www.postgresql.org/download/windows/
- Mac: `brew install postgresql@15`

**Option 2: Docker (Recommended)**
```bash
# Install Docker Desktop first
docker --version

# Run PostgreSQL
docker run --name sikades-postgres \
  -e POSTGRES_PASSWORD=sikades123 \
  -e POSTGRES_DB=sikades_api \
  -p 5432:5432 \
  -d postgres:15
```

### 1.5 Install Git

```bash
git --version
```

If not installed: https://git-scm.com/downloads

---

## 2. ENVIRONMENT SETUP

### 2.1 Create Project Directory

```bash
mkdir sikades-api-gateway
cd sikades-api-gateway
```

### 2.2 Initialize Git

```bash
git init
git branch -M main
```

### 2.3 Create .gitignore

```bash
# .gitignore
node_modules/
dist/
.env
.env.local
.env.production
coverage/
.DS_Store
*.log
.vscode/settings.json
```

---

## 3. CREATE NEW PROJECT

### 3.1 Generate NestJS Project

```bash
nest new . --package-manager npm
```

**Questions:**
- Project name: `sikades-api-gateway`
- Package manager: `npm`

**Wait for installation...** (~2-3 minutes)

### 3.2 Test Initial Setup

```bash
npm run start:dev
```

**Expected output:**
```
[Nest] INFO [NestFactory] Starting Nest application...
[Nest] INFO [InstanceLoader] AppModule dependencies initialized
[Nest] INFO Application listening on http://localhost:3000
```

**Test in browser:**
```
http://localhost:3000
```

Should see: `Hello World!`

✅ **Base project works!**

---

## 4. PROJECT STRUCTURE

### 4.1 Recommended Folder Structure

```
sikades-api-gateway/
├── src/
│   ├── main.ts
│   ├── app.module.ts
│   ├── app.controller.ts
│   ├── app.service.ts
│   │
│   ├── config/                    # Configuration
│   │   ├── database.config.ts
│   │   ├── jwt.config.ts
│   │   └── app.config.ts
│   │
│   ├── common/                    # Shared utilities
│   │   ├── decorators/
│   │   │   ├── current-user.decorator.ts
│   │   │   └── roles.decorator.ts
│   │   ├── guards/
│   │   │   ├── jwt-auth.guard.ts
│   │   │   └── roles.guard.ts
│   │   ├── interceptors/
│   │   │   ├── logging.interceptor.ts
│   │   │   └── transform.interceptor.ts
│   │   ├── filters/
│   │   │   └── http-exception.filter.ts
│   │   ├── pipes/
│   │   │   └── validation.pipe.ts
│   │   └── interfaces/
│   │       └── api-response.interface.ts
│   │
│   ├── auth/                      # Authentication module
│   │   ├── auth.module.ts
│   │   ├── auth.controller.ts
│   │   ├── auth.service.ts
│   │   ├── strategies/
│   │   │   ├── jwt.strategy.ts
│   │   │   └── local.strategy.ts
│   │   └── dto/
│   │       ├── login.dto.ts
│   │       └── register.dto.ts
│   │
│   ├── users/                     # Users module
│   │   ├── users.module.ts
│   │   ├── users.controller.ts
│   │   ├── users.service.ts
│   │   ├── entities/
│   │   │   └── user.entity.ts
│   │   └── dto/
│   │       ├── create-user.dto.ts
│   │       └── update-user.dto.ts
│   │
│   ├── dashboard/                 # Dashboard modules
│   │   ├── desa/
│   │   │   ├── desa-dashboard.module.ts
│   │   │   ├── desa-dashboard.controller.ts
│   │   │   ├── desa-dashboard.service.ts
│   │   │   └── dto/
│   │   ├── kecamatan/
│   │   ├── kabupaten/
│   │   └── provinsi/
│   │
│   └── database/
│       ├── migrations/
│       └── seeds/
│
├── test/
│   ├── app.e2e-spec.ts
│   └── jest-e2e.json
│
├── .env.example
├── .env
├── .eslintrc.js
├── .prettierrc
├── nest-cli.json
├── package.json
├── tsconfig.json
└── README.md
```

### 4.2 Create Folder Structure

```bash
# Create all folders
mkdir -p src/config
mkdir -p src/common/decorators
mkdir -p src/common/guards
mkdir -p src/common/interceptors
mkdir -p src/common/filters
mkdir -p src/common/pipes
mkdir -p src/common/interfaces
mkdir -p src/auth/strategies
mkdir -p src/auth/dto
mkdir -p src/users/entities
mkdir -p src/users/dto
mkdir -p src/dashboard/desa/dto
mkdir -p src/dashboard/kecamatan
mkdir -p src/dashboard/kabupaten
mkdir -p src/dashboard/provinsi
mkdir -p src/database/migrations
mkdir -p src/database/seeds
```

---

## 5. DATABASE SETUP

### 5.1 Install Dependencies

```bash
npm install @nestjs/typeorm typeorm pg
npm install @nestjs/config
```

### 5.2 Database Configuration (Supabase)

**⚠️ IMPORTANT:** SIKADES uses **Supabase PostgreSQL** (cloud-hosted), NOT local PostgreSQL!

#### 5.2.1 Get Supabase Credentials

Before proceeding, you need to fill in the **SUPABASE_CONFIG_TEMPLATE.md** file:

1. Open `SUPABASE_CONFIG_TEMPLATE.md` in this repository
2. Login to your Supabase dashboard: https://app.supabase.com
3. Find your SIKADES project
4. Follow instructions in the template to get:
   - Database host, password, connection string
   - API keys (anon and service_role)
5. Fill in all values in the template
6. Copy the completed config to `.env` file

#### 5.2.2 Create .env File

```bash
# .env
NODE_ENV=development
PORT=3000

# ===================================================
# SUPABASE DATABASE CONNECTION (PostgreSQL)
# ===================================================
# ⚠️ Fill these from SUPABASE_CONFIG_TEMPLATE.md!

# Database Host (Supabase)
DB_HOST=db.xxxxxxxxxxxxxxxxxxxxx.supabase.co
# Find in: Supabase Dashboard → Settings → Database → Connection string

# Database Port (Always 5432 for direct connection)
DB_PORT=5432

# Database Username (Always 'postgres')
DB_USERNAME=postgres

# Database Password
DB_PASSWORD=[YOUR_SUPABASE_DB_PASSWORD]
# Find in: Settings → Database → Database Password

# Database Name (Always 'postgres')
DB_DATABASE=postgres

# SSL Configuration (REQUIRED for Supabase!)
DB_SSL=true
DB_SSL_REJECT_UNAUTHORIZED=false

# Connection Pool Settings
DB_POOL_MIN=2
DB_POOL_MAX=10
DB_POOL_IDLE_TIMEOUT=10000
DB_POOL_CONNECTION_TIMEOUT=30000

# Alternative: Full Connection String (Optional)
# DATABASE_URL=postgresql://postgres:[password]@db.[project-ref].supabase.co:5432/postgres?sslmode=require

# ===================================================
# SUPABASE API (Optional - for Supabase features)
# ===================================================
SUPABASE_URL=https://xxxxxxxxxxxxxxxxxxxxx.supabase.co
SUPABASE_ANON_KEY=[anon_key_from_supabase]
SUPABASE_SERVICE_ROLE_KEY=[service_role_key_from_supabase]

# ===================================================
# JWT (Application Tokens)
# ===================================================
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRATION=15m
JWT_REFRESH_SECRET=your-super-secret-refresh-key
JWT_REFRESH_EXPIRATION=7d

# ===================================================
# API CONFIGURATION
# ===================================================
API_PREFIX=api/v1
API_RATE_LIMIT=1000

# CORS (Allow Flutter apps & web dashboard)
CORS_ORIGIN=http://localhost:3000,http://localhost:5173,http://localhost:8080

# ===================================================
# SHARED DATABASE WARNING!
# ===================================================
# ⚠️ This API Gateway shares the SAME database with sikades-lite (CodeIgniter)
# ⚠️ DO NOT drop or alter existing tables without coordination
# ⚠️ Use migrations for new tables with prefix 'api_*' to avoid conflicts
```

#### 5.2.3 Create .env.example (for Git)

```bash
# Copy .env to .env.example (with placeholders, no sensitive data)
cp .env .env.example

# Edit .env.example to replace actual values with placeholders
# Example: DB_PASSWORD=[YOUR_SUPABASE_DB_PASSWORD]
```

**Add to .gitignore:**
```bash
# .gitignore (already should have these)
.env
.env.local
.env.production
SUPABASE_CONFIG.md  # After filling the template
```

### 5.3 Create Database Config

```typescript
// src/config/database.config.ts
import { TypeOrmModuleOptions } from '@nestjs/typeorm';
import { ConfigService } from '@nestjs/config';

export const getDatabaseConfig = (
  configService: ConfigService,
): TypeOrmModuleOptions => {
  const isProduction = configService.get('NODE_ENV') === 'production';
  const useSsl = configService.get('DB_SSL', 'true') === 'true';

  return {
    type: 'postgres',
    host: configService.get('DB_HOST'),
    port: parseInt(configService.get('DB_PORT', '5432')),
    username: configService.get('DB_USERNAME'),
    password: configService.get('DB_PASSWORD'),
    database: configService.get('DB_DATABASE'),
    
    // SSL Configuration (Required for Supabase)
    ssl: useSsl
      ? {
          rejectUnauthorized: configService.get('DB_SSL_REJECT_UNAUTHORIZED', 'false') === 'true',
        }
      : false,
    
    // Connection Pool Settings
    extra: {
      min: parseInt(configService.get('DB_POOL_MIN', '2')),
      max: parseInt(configService.get('DB_POOL_MAX', '10')),
      idleTimeoutMillis: parseInt(configService.get('DB_POOL_IDLE_TIMEOUT', '10000')),
      connectionTimeoutMillis: parseInt(configService.get('DB_POOL_CONNECTION_TIMEOUT', '30000')),
    },
    
    // Entity & Migration Settings
    entities: [__dirname + '/../**/*.entity{.ts,.js}'],
    
    // ⚠️ IMPORTANT: Set to false in production!
    // Supabase database is shared with sikades-lite (CodeIgniter)
    synchronize: false, // NEVER use synchronize with shared database!
    
    logging: !isProduction,
    
    // Migrations
    migrations: [__dirname + '/../database/migrations/*{.ts,.js}'],
    migrationsRun: false, // Run migrations manually with npm run migration:run
    migrationsTableName: 'typeorm_migrations', // Separate from CodeIgniter migrations
  };
};
```

**⚠️ IMPORTANT NOTES:**

1. **`synchronize: false`** - NEVER set to true when sharing database! This will auto-create/modify tables and can break sikades-lite.

2. **Manual Migrations** - Always create and run migrations manually:
   ```bash
   npm run typeorm migration:create -- src/database/migrations/CreateApiTables
   npm run typeorm migration:run
   ```

3. **SSL Required** - Supabase requires SSL connection. The config handles this automatically.

### 5.4 Update app.module.ts

```typescript
// src/app.module.ts
import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { getDatabaseConfig } from './config/database.config';
import { AppController } from './app.controller';
import { AppService } from './app.service';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
    }),
    TypeOrmModule.forRootAsync({
      imports: [ConfigModule],
      useFactory: getDatabaseConfig,
      inject: [ConfigService],
    }),
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
```

### 5.5 Test Database Connection

```bash
npm run start:dev
```

**Expected:**
```
[Nest] INFO [TypeOrmModule] Database connection established
```

✅ **Database connected!**

---

## 6. AUTHENTICATION MODULE

### 6.1 Install Dependencies

```bash
npm install @nestjs/passport passport passport-local passport-jwt
npm install @nestjs/jwt bcrypt
npm install -D @types/passport-local @types/passport-jwt @types/bcrypt
```

### 6.2 Generate Auth Module

```bash
nest generate module auth
nest generate service auth
nest generate controller auth
```

### 6.3 Create User Entity

```typescript
// src/users/entities/user.entity.ts
import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('users')
export class User {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ unique: true })
  username: string;

  @Column()
  password: string;

  @Column()
  email: string;

  @Column({ nullable: true })
  phone: string;

  @Column({
    type: 'enum',
    enum: ['kepala_desa', 'camat', 'kabag_bpkad', 'gubernur', 'admin'],
    default: 'kepala_desa',
  })
  role: string;

  @Column({
    type: 'enum',
    enum: ['desa', 'kecamatan', 'kabupaten', 'provinsi'],
    default: 'desa',
  })
  level: string;

  @Column({ name: 'kode_desa', nullable: true })
  kodeDesa: string;

  @Column({ name: 'nama_desa', nullable: true })
  namaDesa: string;

  @Column({ name: 'kode_kecamatan', nullable: true })
  kodeKecamatan: string;

  @Column({ name: 'kode_kabupaten', nullable: true })
  kodeKabupaten: string;

  @Column({ name: 'kode_provinsi', nullable: true })
  kodeProvinsi: string;

  @Column({ name: 'photo_url', nullable: true })
  photoUrl: string;

  @Column({ default: true })
  isActive: boolean;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;

  @Column({ name: 'last_login_at', nullable: true })
  lastLoginAt: Date;
}
```

### 6.4 Create DTOs

```typescript
// src/auth/dto/login.dto.ts
import { IsString, IsNotEmpty, MinLength } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class LoginDto {
  @ApiProperty({ example: 'admin_desa' })
  @IsString()
  @IsNotEmpty()
  username: string;

  @ApiProperty({ example: 'password123' })
  @IsString()
  @IsNotEmpty()
  @MinLength(6)
  password: string;

  @ApiProperty({ example: '550e8400-e29b-41d4-a716-446655440000', required: false })
  @IsString()
  deviceId?: string;
}
```

```typescript
// src/auth/dto/auth-response.dto.ts
import { ApiProperty } from '@nestjs/swagger';

export class UserResponseDto {
  @ApiProperty()
  id: number;

  @ApiProperty()
  username: string;

  @ApiProperty()
  email: string;

  @ApiProperty()
  role: string;

  @ApiProperty()
  level: string;

  @ApiProperty()
  kodeDesa: string;

  @ApiProperty()
  namaDesa: string;
}

export class AuthResponseDto {
  @ApiProperty()
  accessToken: string;

  @ApiProperty()
  refreshToken: string;

  @ApiProperty()
  tokenType: string;

  @ApiProperty()
  expiresIn: number;

  @ApiProperty({ type: UserResponseDto })
  user: UserResponseDto;
}
```

### 6.5 Create Auth Service

```typescript
// src/auth/auth.service.ts
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import * as bcrypt from 'bcrypt';
import { User } from '../users/entities/user.entity';
import { LoginDto } from './dto/login.dto';
import { AuthResponseDto } from './dto/auth-response.dto';

@Injectable()
export class AuthService {
  constructor(
    @InjectRepository(User)
    private usersRepository: Repository<User>,
    private jwtService: JwtService,
  ) {}

  async validateUser(username: string, password: string): Promise<User | null> {
    const user = await this.usersRepository.findOne({
      where: { username },
    });

    if (user && (await bcrypt.compare(password, user.password))) {
      return user;
    }

    return null;
  }

  async login(loginDto: LoginDto): Promise<AuthResponseDto> {
    const user = await this.validateUser(loginDto.username, loginDto.password);

    if (!user) {
      throw new UnauthorizedException('Invalid credentials');
    }

    if (!user.isActive) {
      throw new UnauthorizedException('Account is disabled');
    }

    // Update last login
    await this.usersRepository.update(user.id, {
      lastLoginAt: new Date(),
    });

    const payload = {
      sub: user.id,
      username: user.username,
      role: user.role,
      level: user.level,
      kodeDesa: user.kodeDesa,
    };

    return {
      accessToken: this.jwtService.sign(payload),
      refreshToken: this.jwtService.sign(payload, { expiresIn: '7d' }),
      tokenType: 'Bearer',
      expiresIn: 900, // 15 minutes
      user: {
        id: user.id,
        username: user.username,
        email: user.email,
        role: user.role,
        level: user.level,
        kodeDesa: user.kodeDesa,
        namaDesa: user.namaDesa,
      },
    };
  }

  async refreshToken(refreshToken: string): Promise<{ accessToken: string }> {
    try {
      const payload = this.jwtService.verify(refreshToken);
      
      const newPayload = {
        sub: payload.sub,
        username: payload.username,
        role: payload.role,
        level: payload.level,
        kodeDesa: payload.kodeDesa,
      };

      return {
        accessToken: this.jwtService.sign(newPayload),
      };
    } catch (error) {
      throw new UnauthorizedException('Invalid refresh token');
    }
  }
}
```

### 6.6 Create JWT Strategy

```typescript
// src/auth/strategies/jwt.strategy.ts
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { ExtractJwt, Strategy } from 'passport-jwt';
import { ConfigService } from '@nestjs/config';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { User } from '../../users/entities/user.entity';

@Injectable()
export class JwtStrategy extends PassportStrategy(Strategy) {
  constructor(
    private configService: ConfigService,
    @InjectRepository(User)
    private usersRepository: Repository<User>,
  ) {
    super({
      jwtFromRequest: ExtractJwt.fromAuthHeaderAsBearerToken(),
      ignoreExpiration: false,
      secretOrKey: configService.get('JWT_SECRET'),
    });
  }

  async validate(payload: any) {
    const user = await this.usersRepository.findOne({
      where: { id: payload.sub },
    });

    if (!user || !user.isActive) {
      throw new UnauthorizedException();
    }

    return {
      userId: payload.sub,
      username: payload.username,
      role: payload.role,
      level: payload.level,
      kodeDesa: payload.kodeDesa,
    };
  }
}
```

### 6.7 Create Auth Controller

```typescript
// src/auth/auth.controller.ts
import { Controller, Post, Body, HttpCode, HttpStatus } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse } from '@nestjs/swagger';
import { AuthService } from './auth.service';
import { LoginDto } from './dto/login.dto';
import { AuthResponseDto } from './dto/auth-response.dto';

@ApiTags('Authentication')
@Controller('auth')
export class AuthController {
  constructor(private readonly authService: AuthService) {}

  @Post('login')
  @HttpCode(HttpStatus.OK)
  @ApiOperation({ summary: 'Login with username and password' })
  @ApiResponse({
    status: 200,
    description: 'Login successful',
    type: AuthResponseDto,
  })
  @ApiResponse({ status: 401, description: 'Invalid credentials' })
  async login(@Body() loginDto: LoginDto) {
    const result = await this.authService.login(loginDto);
    return {
      success: true,
      message: 'Login successful',
      data: result,
    };
  }

  @Post('refresh')
  @HttpCode(HttpStatus.OK)
  @ApiOperation({ summary: 'Refresh access token' })
  async refresh(@Body() body: { refreshToken: string }) {
    const result = await this.authService.refreshToken(body.refreshToken);
    return {
      success: true,
      data: result,
    };
  }
}
```

### 6.8 Update Auth Module

```typescript
// src/auth/auth.module.ts
import { Module } from '@nestjs/common';
import { JwtModule } from '@nestjs/jwt';
import { PassportModule } from '@nestjs/passport';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AuthService } from './auth.service';
import { AuthController } from './auth.controller';
import { JwtStrategy } from './strategies/jwt.strategy';
import { User } from '../users/entities/user.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([User]),
    PassportModule,
    JwtModule.registerAsync({
      imports: [ConfigModule],
      useFactory: async (configService: ConfigService) => ({
        secret: configService.get('JWT_SECRET'),
        signOptions: {
          expiresIn: configService.get('JWT_EXPIRATION', '15m'),
        },
      }),
      inject: [ConfigService],
    }),
  ],
  controllers: [AuthController],
  providers: [AuthService, JwtStrategy],
  exports: [AuthService],
})
export class AuthModule {}
```

### 6.9 Create Database Seed (Test User)

```bash
npm install -D ts-node
```

```typescript
// src/database/seeds/create-admin.seed.ts
import { DataSource } from 'typeorm';
import * as bcrypt from 'bcrypt';
import { User } from '../../users/entities/user.entity';

export async function seedAdminUser(dataSource: DataSource) {
  const userRepository = dataSource.getRepository(User);

  const existingAdmin = await userRepository.findOne({
    where: { username: 'admin' },
  });

  if (existingAdmin) {
    console.log('Admin user already exists');
    return;
  }

  const hashedPassword = await bcrypt.hash('admin123', 10);

  const admin = userRepository.create({
    username: 'admin',
    password: hashedPassword,
    email: 'admin@sikades.id',
    role: 'admin',
    level: 'provinsi',
    isActive: true,
  });

  await userRepository.save(admin);
  console.log('Admin user created successfully');
}
```

### 6.10 Update main.ts (Add Seed Command)

```typescript
// src/main.ts
import { NestFactory } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { SwaggerModule, DocumentBuilder } from '@nestjs/swagger';
import { AppModule } from './app.module';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);

  // Enable CORS
  app.enableCors({
    origin: process.env.CORS_ORIGIN?.split(',') || '*',
    credentials: true,
  });

  // Global prefix
  app.setGlobalPrefix(process.env.API_PREFIX || 'api/v1');

  // Global validation pipe
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      forbidNonWhitelisted: true,
      transform: true,
    }),
  );

  // Swagger documentation
  const config = new DocumentBuilder()
    .setTitle('SIKADES API Gateway')
    .setDescription('API documentation for SIKADES multi-level dashboard')
    .setVersion('1.0')
    .addBearerAuth()
    .addTag('Authentication')
    .addTag('Dashboard Desa')
    .addTag('Dashboard Kecamatan')
    .addTag('Dashboard Kabupaten')
    .addTag('Dashboard Provinsi')
    .build();

  const document = SwaggerModule.createDocument(app, config);
  SwaggerModule.setup('docs', app, document);

  const port = process.env.PORT || 3000;
  await app.listen(port);

  console.log(`🚀 Application is running on: http://localhost:${port}`);
  console.log(`📚 API Documentation: http://localhost:${port}/docs`);
}
bootstrap();
```

---

## 7. FIRST API ENDPOINT

### 7.1 Test Login Endpoint

```bash
npm run start:dev
```

### 7.2 Create Test User (PostgreSQL)

```sql
-- Connect to database
psql -U postgres -d sikades_api

-- Create test user
INSERT INTO users (
  username, password, email, role, level,
  kode_desa, nama_desa, is_active, created_at, updated_at
) VALUES (
  'admin_desa',
  '$2b$10$YourHashedPasswordHere', -- bcrypt hash of 'password123'
  'admin@desalembang.id',
  'kepala_desa',
  'desa',
  '3216012001',
  'Lembang',
  true,
  NOW(),
  NOW()
);
```

**Or use API to hash:**
```typescript
import * as bcrypt from 'bcrypt';
const hash = await bcrypt.hash('password123', 10);
console.log(hash);
```

### 7.3 Test with cURL

```bash
curl -X POST http://localhost:3000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin_desa",
    "password": "password123"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "accessToken": "eyJhbGc...",
    "refreshToken": "eyJhbG...",
    "tokenType": "Bearer",
    "expiresIn": 900,
    "user": {
      "id": 1,
      "username": "admin_desa",
      "email": "admin@desalembang.id",
      "role": "kepala_desa",
      "level": "desa",
      "kodeDesa": "3216012001",
      "namaDesa": "Lembang"
    }
  }
}
```

✅ **Authentication works!**

---

## 8. VALIDATION & DTOs

### 8.1 Install Class Validator

```bash
npm install class-validator class-transformer
```

### 8.2 Create Base Response Interface

```typescript
// src/common/interfaces/api-response.interface.ts
export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data?: T;
  errors?: any;
  meta?: PaginationMeta;
}

export interface PaginationMeta {
  currentPage: number;
  perPage: number;
  total: number;
  totalPages: number;
  hasNext: boolean;
  hasPrev: boolean;
}
```

### 8.3 Create Transform Interceptor

```typescript
// src/common/interceptors/transform.interceptor.ts
import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Response<T> {
  success: boolean;
  data: T;
}

@Injectable()
export class TransformInterceptor<T>
  implements NestInterceptor<T, Response<T>>
{
  intercept(
    context: ExecutionContext,
    next: CallHandler,
  ): Observable<Response<T>> {
    return next.handle().pipe(
      map((data) => ({
        success: true,
        ...data,
      })),
    );
  }
}
```

---

## 9. ERROR HANDLING

### 9.1 Create HTTP Exception Filter

```typescript
// src/common/filters/http-exception.filter.ts
import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpException,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';

@Catch(HttpException)
export class HttpExceptionFilter implements ExceptionFilter {
  catch(exception: HttpException, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const status = exception.getStatus();
    const exceptionResponse = exception.getResponse();

    const message =
      typeof exceptionResponse === 'string'
        ? exceptionResponse
        : (exceptionResponse as any).message || 'An error occurred';

    response.status(status).json({
      success: false,
      message,
      errors: {
        code: HttpStatus[status],
        details: exceptionResponse,
      },
      timestamp: new Date().toISOString(),
    });
  }
}
```

### 9.2 Apply Globally in main.ts

```typescript
// src/main.ts
import { HttpExceptionFilter } from './common/filters/http-exception.filter';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  // Global exception filter
  app.useGlobalFilters(new HttpExceptionFilter());
  
  // ... rest of code
}
```

---

## 10. TESTING

### 10.1 Unit Test Example

```typescript
// src/auth/auth.service.spec.ts
import { Test, TestingModule } from '@nestjs/testing';
import { JwtService } from '@nestjs/jwt';
import { getRepositoryToken } from '@nestjs/typeorm';
import { AuthService } from './auth.service';
import { User } from '../users/entities/user.entity';

describe('AuthService', () => {
  let service: AuthService;

  const mockUserRepository = {
    findOne: jest.fn(),
    update: jest.fn(),
  };

  const mockJwtService = {
    sign: jest.fn(() => 'test-token'),
    verify: jest.fn(),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AuthService,
        {
          provide: getRepositoryToken(User),
          useValue: mockUserRepository,
        },
        {
          provide: JwtService,
          useValue: mockJwtService,
        },
      ],
    }).compile();

    service = module.get<AuthService>(AuthService);
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  describe('login', () => {
    it('should return auth response with tokens', async () => {
      const mockUser = {
        id: 1,
        username: 'test',
        password: 'hashed',
        role: 'admin',
        isActive: true,
      };

      mockUserRepository.findOne.mockResolvedValue(mockUser);

      const result = await service.login({
        username: 'test',
        password: 'password',
      });

      expect(result).toHaveProperty('accessToken');
      expect(result).toHaveProperty('refreshToken');
    });
  });
});
```

### 10.2 Run Tests

```bash
# Unit tests
npm run test

# E2E tests
npm run test:e2e

# Test coverage
npm run test:cov
```

---

## 11. SWAGGER DOCUMENTATION

### 11.1 Install Swagger

```bash
npm install @nestjs/swagger
```

### 11.2 Access Documentation

```
http://localhost:3000/docs
```

You'll see interactive API documentation!

---

## 12. DEPLOYMENT

### 12.1 Create Dockerfile

```dockerfile
# Dockerfile
FROM node:20-alpine AS builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY --from=builder /app/dist ./dist

EXPOSE 3000

CMD ["node", "dist/main"]
```

### 12.2 Create docker-compose.yml

```yaml
# docker-compose.yml
version: '3.8'

services:
  postgres:
    image: postgres:15
    environment:
      POSTGRES_DB: sikades_api
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: sikades123
    ports:
      - '5432:5432'
    volumes:
      - postgres_data:/var/lib/postgresql/data

  api:
    build: .
    ports:
      - '3000:3000'
    environment:
      DB_HOST: postgres
      DB_PORT: 5432
      DB_USERNAME: postgres
      DB_PASSWORD: sikades123
      DB_DATABASE: sikades_api
      JWT_SECRET: your-secret-key
    depends_on:
      - postgres

volumes:
  postgres_data:
```

### 12.3 Build & Run with Docker

```bash
docker-compose up -d
```

### 12.4 Deploy to Production (VPS)

```bash
# 1. Clone repository
git clone https://github.com/your-org/sikades-api-gateway.git
cd sikades-api-gateway

# 2. Create .env.production
cp .env.example .env.production
# Edit with production values

# 3. Build Docker image
docker build -t sikades-api:latest .

# 4. Run with docker-compose
docker-compose -f docker-compose.prod.yml up -d

# 5. Setup reverse proxy (Nginx)
# See deployment guide for full Nginx config
```

---

## APPENDIX: Useful Commands

```bash
# Generate module
nest generate module <name>

# Generate controller
nest generate controller <name>

# Generate service
nest generate service <name>

# Generate full resource (CRUD)
nest generate resource <name>

# Run development
npm run start:dev

# Build for production
npm run build

# Run production
npm run start:prod

# Run tests
npm run test
npm run test:watch
npm run test:cov

# Lint
npm run lint

# Format code
npm run format
```

---

## NEXT STEPS

1. ✅ Implement Dashboard Desa endpoints
2. ✅ Add caching with Redis
3. ✅ Implement rate limiting
4. ✅ Add logging (Winston)
5. ✅ Setup CI/CD (GitHub Actions)

---

**End of Implementation Guide**

**Need Help?**
- NestJS Docs: https://docs.nestjs.com
- TypeORM Docs: https://typeorm.io

**Created by:** SIKADES Development Team  
**Last Updated:** 26 Desember 2024
