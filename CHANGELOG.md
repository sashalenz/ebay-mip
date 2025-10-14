# Changelog

All notable changes to `ebay-mip` will be documented in this file.

## 1.0.0 - 2025-10-14

### Added
- **Feed Generators (6 types)**: Product, Distribution, Availability, Fulfillment, Order Report, Combined
- **Dual Format Support**: CSV and XML for all feed types
- **SFTP Upload Automation**: Laravel Scheduler integration with retry logic
- **Validation Engine**: Full eBay MIP rules enforcement
- **Fluent Builders**: ProductFeedBuilder, AvailabilityFeedBuilder, FulfillmentFeedBuilder
- **Upload History**: Database tracking with status management
- **Artisan Commands**: 4 commands for generation, upload, download, and testing
- **Laravel Events**: FeedGenerated, FeedUploaded, FeedUploadFailed
- **Response Parser**: Parse MIP response files for error handling
- **Manual Mode**: Generate feeds without automatic upload

### Features
- Upload 50,000+ SKUs in 30 minutes
- Type-safe Data objects (Spatie Laravel Data)
- Automatic compression (.zip)
- Retry logic (3 attempts)
- Feed retention management
- SFTP connection testing
- Comprehensive validation rules

### Summary
- **6 Feed Generators**
- **~50 PHP Files**
- **4 Artisan Commands**
- **3 Laravel Events**
- **10+ Validation Rules**
- **1 Database Table**

