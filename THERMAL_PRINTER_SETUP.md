# 🖨️ Thermal Printer Setup & Testing Guide - Saliya In Hotel POS System

## 📋 Overview

This guide covers the setup, testing, and usage of the 80mm Thermal Receipt POS Printer (YHD-80E) for invoice printing in the POS system.

## 🎯 Printer Specifications

- **Model:** YHD-80E 80mm Thermal Receipt POS Printer
- **Paper Width:** 80mm (3.15 inches)
- **Print Width:** 32 characters per line
- **Interface:** USB
- **Paper Type:** Thermal paper roll
- **Print Speed:** 250mm/s

## 🧪 Testing Without Physical Printer

### ✅ **Method 1: Thermal Printer Preview (Recommended)**

1. **Complete an Order:**
   - Go to POS system (`/pos`) or Tables system (`/tables`)
   - Create a test order with multiple items
   - Complete the payment process

2. **Access Print Options:**
   - After successful payment, the "Print Invoice" section will appear
   - Click the **"Thermal Printer"** button

3. **Review Preview:**
   - A new window will open showing the thermal printer formatted text
   - The preview displays exactly how the text will appear on your thermal printer
   - Text is formatted for 32-character width (80mm paper)
   - Use the "Print Preview" button to see how it would look on paper

**What You'll See:**
- Company header with logo and contact details
- Order information (ID, date, time, type)
- Customer details (if available)
- Itemized list with quantities and prices
- Payment breakdown
- Footer with thank you message

### ✅ **Method 2: Web Print Preview**

1. **Complete an Order:**
   - Same as above - create and complete a test order

2. **Access Web Print:**
   - Click the **"Web Print"** button in the print options

3. **Review HTML Invoice:**
   - A new window opens with a formatted HTML invoice
   - This shows how the invoice would look on regular paper
   - Use browser print preview (Ctrl+P) to see print layout
   - Good for testing layout and content accuracy

### ✅ **Method 3: Download Text File**

1. **Complete an Order:**
   - Create and complete a test order

2. **Download Invoice:**
   - Click the **"Download TXT"** button

3. **Review Text File:**
   - A `.txt` file will download to your computer
   - Open with any text editor (Notepad, VS Code, etc.)
   - Contains the exact same text that would be sent to the thermal printer
   - Useful for sharing or detailed inspection

## 🔧 Physical Printer Setup

### Step 1: Hardware Connection

1. **Connect Printer:**
   - Plug the USB cable into your computer
   - Ensure the printer is powered on
   - Wait for Windows to detect the device

2. **Install Drivers:**
   - Download drivers from manufacturer website
   - Install the printer software
   - Test print a sample receipt

3. **Load Paper:**
   - Open the printer cover
   - Insert 80mm thermal paper roll
   - Ensure paper is properly aligned
   - Close the cover

### Step 2: System Configuration

1. **Set as Default Printer:**
   - Go to Windows Settings > Devices > Printers
   - Find your thermal printer
   - Set as default printer (optional)

2. **Test Connection:**
   - Print a test page from Windows
   - Verify paper feeds correctly
   - Check print quality

### Step 3: Browser Configuration

1. **Enable Printing:**
   - Ensure your browser allows pop-ups for the POS system
   - Test print functionality with a sample order

2. **Print Settings:**
   - Set paper size to 80mm x 297mm (A4 width)
   - Disable headers and footers
   - Set margins to minimum

## 🎨 Print Features

### ✅ **Invoice Content**

- **Header Section:**
  - Company name and logo
  - Address and contact information
  - Date and time stamp

- **Order Details:**
  - Order ID and type (Dine-in/Takeaway)
  - Customer information (if available)
  - Table number (for dine-in orders)

- **Item List:**
  - Item names and descriptions
  - Quantities and unit prices
  - Portion types (Full/Half)
  - Individual item totals

- **Payment Summary:**
  - Subtotal amount
  - Discount (if applicable)
  - Total amount
  - Payment method
  - Amount paid and change

- **Footer:**
  - Thank you message
  - Website URL
  - Powered by information

### ✅ **Formatting Features**

- **Text Alignment:**
  - Centered headers and titles
  - Left-aligned item lists
  - Right-aligned prices and totals

- **Character Width:**
  - Optimized for 32-character width
  - Proper spacing and padding
  - Clean line breaks

- **Special Characters:**
  - Dashes and equal signs for separators
  - Currency symbols (Rs.)
  - Decimal formatting

## 🚀 Usage Instructions

### For Takeaway Orders:

1. **Create Order:**
   - Select items and quantities
   - Choose portion sizes
   - Add customer information

2. **Process Payment:**
   - Select payment method
   - Enter amount received
   - Complete payment

3. **Print Invoice:**
   - Click "Thermal Printer" for physical print
   - Click "Web Print" for browser preview
   - Click "Download TXT" for text file

### For Dine-in Orders:

1. **Assign to Table:**
   - Select table number
   - Add items to order
   - Process payment

2. **Close Order:**
   - Complete payment process
   - Clear table assignment

3. **Print Invoice:**
   - Same print options as takeaway orders

## 🔍 Troubleshooting

### Common Issues:

1. **Printer Not Detected:**
   - Check USB connection
   - Restart printer and computer
   - Reinstall drivers

2. **Print Quality Issues:**
   - Clean print head
   - Replace thermal paper
   - Check print density settings

3. **Paper Jams:**
   - Remove stuck paper carefully
   - Check paper alignment
   - Ensure paper roll is properly loaded

4. **Text Alignment Problems:**
   - Verify 32-character width setting
   - Check printer driver settings
   - Test with sample text

### Testing Checklist:

- [ ] Thermal printer preview shows correctly
- [ ] Web print preview displays properly
- [ ] Text file download works
- [ ] Physical printer prints when connected
- [ ] Text alignment is correct
- [ ] All order details are included
- [ ] Payment information is accurate
- [ ] Company details are displayed

## 📞 Support

If you encounter issues:

1. **Check this guide** for common solutions
2. **Test with preview options** before connecting printer
3. **Verify printer drivers** are up to date
4. **Contact technical support** if problems persist

## 🔄 Updates

This guide will be updated as new features are added to the printing system. Check back regularly for the latest information. 