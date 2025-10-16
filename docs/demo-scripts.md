# Demo Scripts — ContentCraft AI

Step-by-step scenarios to demonstrate plugin capabilities.

---

## 🎬 Demo 1: Generate a Blog Post

**Duration**: ~3 minutes

### Scenario:
A WordPress blogger wants to create an SEO-optimized article about "Best WordPress Security Plugins 2024".

### Steps:

1. **Navigate to Content Studio**
   - Go to WordPress Admin → **ContentCraft AI** → **Content Studio**

2. **Fill the Form**
   - **Topic**: Best WordPress Security Plugins 2024
   - **Target Audience**: WordPress site owners
   - **Keywords**: wordpress security, security plugins, malware protection, firewall
   - **Tone**: Professional
   - **Length**: Long (2000+ words)
   - **Language**: English
   - **Sections**: ✓ Title, ✓ Excerpt, ✓ Body, ✓ Meta

3. **Generate Content**
   - Click **Generate Content** button
   - Wait 3-5 seconds (progress indicator shows)

4. **Review Output**
   - **Title**: "10 Best WordPress Security Plugins to Protect Your Site in 2024"
   - **Excerpt**: "Protect your WordPress site from hackers with these top-rated..."
   - **Body**: Complete article with H2/H3 headings, bullet points, comparisons
   - **Meta Title**: "Best WordPress Security Plugins 2024 | Complete Guide"
   - **Meta Description**: "Discover the top 10 WordPress security plugins..."
   - **Slug**: best-wordpress-security-plugins-2024
   - **Internal Links**: Links to related security articles
   - **Schema**: Article schema JSON-LD

5. **Insert to Editor**
   - Click **Insert to Editor** button
   - Content appears in Gutenberg/Classic Editor
   - Meta fields automatically populated

6. **Publish**
   - Review and edit as needed
   - Click **Publish**

### Expected Result:
✅ SEO-optimized blog post created in under 5 minutes  
✅ Proper heading structure (H1, H2, H3)  
✅ Internal links to related content  
✅ Schema markup for better search visibility  

---

## 🛒 Demo 2: Generate WooCommerce Product Content

**Duration**: ~2 minutes

### Scenario:
A WooCommerce store owner needs comprehensive descriptions for a new product.

### Steps:

1. **Edit Product**
   - Go to **Products** → **Add New** (or edit existing)
   - Enter basic info: **Product Name**: "Stainless Steel Water Bottle"

2. **Open ContentCraft AI Panel**
   - Scroll to **ContentCraft AI** meta box (right sidebar or below editor)

3. **Fill Product Details**
   - **Category**: Home & Kitchen
   - **Attributes**:
     - Material: Stainless Steel 18/8
     - Capacity: 750ml (25oz)
     - Colors: Silver, Black, Blue
     - Features: Insulated, BPA-free, Leak-proof
   - **Key Features**: 
     - Double-wall vacuum insulation
     - Keeps drinks cold 24h, hot 12h
     - Eco-friendly reusable bottle
   - **USP**: 
     - Lifetime warranty
     - 1% of sales to ocean cleanup
   - **Price**: $29.99
   - **Keywords**: water bottle, insulated, stainless steel, eco-friendly
   - **Tone**: Friendly
   - **Language**: English

4. **Select Sections to Generate**
   - ✓ SEO Title
   - ✓ Short Description
   - ✓ Long Description
   - ✓ Bullet Features
   - ✓ FAQs
   - ✓ Meta Description
   - ✓ Tags
   - ✓ Cross-Sell Suggestions

5. **Generate**
   - Click **Generate Product Content**
   - Wait 2-3 seconds

6. **Review & Apply**
   - **SEO Title**: "Insulated Stainless Steel Water Bottle 750ml | 24H Cold"
   - **Short Description**: "Stay hydrated in style with our premium insulated water bottle..."
   - **Long Description**: Full description with sections (Overview, Benefits, Specifications)
   - **Bullet Features**: 5-7 concise feature points
   - **FAQs**: 
     - Q: Does it keep drinks cold all day?
     - A: Yes! Double-wall vacuum insulation keeps beverages cold for up to 24 hours...
   - **Meta Description**: "Shop our premium insulated stainless steel water bottle..."
   - **Tags**: insulated, stainless steel, eco-friendly, reusable, BPA-free
   - **Cross-Sell**: "Insulated Lunch Bag", "Bottle Cleaning Brush"
   
7. **Apply to Product**
   - Click **Apply All** or select individual sections
   - Content auto-fills into WooCommerce fields
   - Tags auto-added to product tags
   - Cross-sell suggestions noted for manual linking

8. **Publish Product**
   - Add images
   - Set price (if not done)
   - Click **Publish**

### Expected Result:
✅ Complete product description with benefit-focused copy  
✅ SEO-optimized title and meta  
✅ Customer-friendly FAQs  
✅ Cross-sell suggestions for increased revenue  

---

## 🖼️ Demo 3: Generate Alt-Text from Product Image

**Duration**: ~1 minute

### Scenario:
A store has 100 products with images but no alt-text (bad for SEO and accessibility).

### Steps:

1. **Open Media Library**
   - Go to **Media** → **Library**
   - Select a product image (e.g., water bottle photo)

2. **Open Attachment Details**
   - Click **Edit** on the image

3. **Generate Alt-Text**
   - Find **ContentCraft AI** section in sidebar
   - Click **Generate Alt-Text** button
   - Wait 1-2 seconds

4. **Review Output**
   - **Alt-Text**: "Stainless steel insulated water bottle in silver on white background"
   - **Description**: "A 750ml stainless steel water bottle featuring double-wall insulation..."
   - **Detected Features**: "Cylindrical shape, screw-top lid, textured grip"

5. **Apply**
   - Click **Apply Alt-Text**
   - Alt-text field automatically filled
   - Click **Update** to save

6. **(Optional) Bulk Alt-Text**
   - For multiple images: Go to **ContentCraft AI** → **Tools** → **Bulk Alt-Text**
   - Select image IDs or upload CSV
   - Process in batches of 10
   - Progress bar shows completion

### Expected Result:
✅ Descriptive, SEO-friendly alt-text under 125 characters  
✅ Better accessibility for visually impaired users  
✅ Improved image SEO rankings  

---

## 🎨 Demo 4: Train Brand Voice

**Duration**: ~5 minutes

### Scenario:
A business wants all generated content to match their unique brand voice.

### Steps:

1. **Navigate to Brand Voice**
   - Go to **ContentCraft AI** → **Brand Voice**

2. **Start Training**
   - Click **Train from Existing Content** button
   - Select options:
     - **Number of Samples**: 20 posts
     - **Post Types**: Posts, Pages
     - **Date Range**: Last 12 months
     - **Language**: English
   - Click **Start Training**

3. **Processing**
   - Plugin collects 20 recent posts
   - Sends to FastAPI for analysis
   - Wait 10-15 seconds (larger sites may take longer)

4. **Review Brand Profile**
   - **Tone**: Conversational yet authoritative
   - **Sentence Length**: Medium (15-20 words avg)
   - **Vocabulary**: Intermediate with occasional technical terms
   - **Paragraph Structure**: Short paragraphs (3-4 sentences)
   - **Common Phrases**: 
     - "Here's the thing:"
     - "In my experience,"
     - "Let me break it down:"
   - **Writing Style**: Storytelling with actionable tips
   - **Confidence Score**: 87%

5. **Enable Profile**
   - Toggle **Apply Brand Voice to Generations**: ON
   - Click **Save Profile**

6. **Test with New Content**
   - Go back to Content Studio
   - Generate a new post
   - Notice content matches your brand's style

### Expected Result:
✅ AI-generated content matches your existing brand voice  
✅ Consistent tone across all generated content  
✅ Time saved on manual editing  

---

## 📦 Demo 5: Bulk Generate Product Descriptions

**Duration**: ~10 minutes (for 50 products)

### Scenario:
A new store imported 100 products via CSV but they all have basic descriptions. Need to enhance them all.

### Steps:

1. **Navigate to Bulk Generator**
   - Go to **ContentCraft AI** → **Bulk Generator**

2. **Select Products**
   - **Filter**: Products without description (or select all)
   - **Categories**: All categories (or specific)
   - **Date Added**: Last 30 days
   - Results: 50 products found
   - Click **Select All** or choose specific products

3. **Configure Generation**
   - **Sections**:
     - ✓ Short Description
     - ✓ Long Description
     - ✓ FAQs
     - ✓ Meta Description
   - **Tone**: Professional
   - **Language**: English
   - **Overwrite Existing**: No (keep existing, fill only empty fields)
   - **Batch Size**: 10 products at a time
   - **Delay Between Batches**: 5 seconds (to respect rate limits)

4. **Start Processing**
   - Click **Start Bulk Generation**
   - Progress shows:
     ```
     Processing: 10 / 50 (20%)
     ████████░░░░░░░░░░░░░░░░░░░░
     Current: Product #234 - "Yoga Mat"
     Status: Generating description...
     ```

5. **Monitor Progress**
   - Each product shows:
     - ✅ Product #230 - Success (850 tokens, 2.1s)
     - ✅ Product #231 - Success (920 tokens, 2.3s)
     - ❌ Product #232 - Failed: Missing required attributes
     - ✅ Product #233 - Success (780 tokens, 1.9s)

6. **Review Results**
   - **Summary**:
     - Total: 50 products
     - Successful: 47
     - Failed: 3
     - Total Tokens Used: 42,500
     - Total Time: 8 minutes 30 seconds
     - Average Time per Product: 10.2 seconds
   
7. **Download Report**
   - Click **Download CSV Report**
   - Contains: Product ID, Name, Status, Tokens, Errors

8. **Fix Failed Products**
   - Click on failed product
   - Review error (e.g., "Missing attributes")
   - Fix manually and re-run

### Expected Result:
✅ 47 products enhanced with professional descriptions  
✅ Consistent quality across all products  
✅ 8 minutes vs. 10+ hours of manual writing  

---

## 🔍 Demo 6: SEO Optimize Existing Content

**Duration**: ~2 minutes

### Scenario:
An old blog post ranks poorly. Use SEO Optimizer to improve it.

### Steps:

1. **Edit Existing Post**
   - Go to **Posts** → Select an older post (e.g., from 2022)
   - Current title: "WordPress Tips"
   - Current meta: Empty or basic

2. **Open SEO Tools**
   - In the editor, find **ContentCraft AI** panel
   - Tab: **SEO Optimizer**

3. **Analyze Content**
   - Click **Analyze Current Content**
   - Wait 2-3 seconds

4. **Review Suggestions**
   - **Current Issues**:
     - Title too generic (2/10 SEO score)
     - No meta description
     - Missing H2 headings
     - No internal links
     - Keyword density too low (0.3%)
   
   - **Improved Title**: "15 Essential WordPress Tips to Boost Your Site in 2024"
   - **Meta Description**: "Discover 15 actionable WordPress tips to improve performance, security, and SEO..."
   - **Slug**: wordpress-tips-2024
   - **Suggested Headings**:
     - H2: "Performance Optimization Tips"
     - H2: "Security Best Practices"
     - H2: "SEO Improvements"
   - **Internal Links**:
     - Link "WordPress security" to existing post: /wordpress-security-guide/
     - Link "page speed" to: /improve-wordpress-speed/
   - **Schema**: Article schema with updated dateModified

5. **Apply Suggestions**
   - Click **Apply SEO Title**
   - Click **Apply Meta Description**
   - Click **Add Suggested Headings** (inserts H2s where appropriate)
   - Click **Add Internal Links** (highlights text and adds links)
   - Click **Add Schema Markup**

6. **Final Check**
   - Review changes in editor
   - SEO Score: 2/10 → 8.5/10
   - Click **Update** post

### Expected Result:
✅ Old post optimized for modern SEO  
✅ Better title and meta for higher CTR  
✅ Proper heading structure  
✅ Strategic internal links  
✅ Schema markup for rich snippets  

---

## 🌐 Demo 7: Multi-language Content Generation

**Duration**: ~3 minutes

### Scenario:
A multilingual site needs the same product description in English, Spanish, and Arabic.

### Steps:

1. **Edit Product** (e.g., "Smart Watch")

2. **Generate in English**
   - ContentCraft AI panel
   - **Language**: English
   - Generate description
   - Save to product

3. **Switch to Spanish**
   - In ContentCraft AI panel
   - **Language**: Español
   - Click **Regenerate in Selected Language**
   - Output: "Reloj Inteligente de Alta Tecnología..."
   - Copy to WPML/Polylang Spanish version

4. **Switch to Arabic**
   - **Language**: العربية
   - Click **Regenerate in Selected Language**
   - Output: "ساعة ذكية متطورة مع..." (RTL formatted)
   - Copy to Arabic version

5. **(Optional) Generate Marketing Snippets**
   - Tab: **Marketing Snippets**
   - **Platform**: Instagram
   - **Language**: English
   - Output: "⌚ Elevate your fitness game with our Smart Watch! Track your health, stay connected, and look stylish. 💪 Shop now! #SmartWatch #Fitness #Tech"
   
   - **Platform**: Email
   - **Language**: English
   - Output:
     - **Subject**: "Introducing the Smart Watch You've Been Waiting For"
     - **Body**: "Hi [Name], We're excited to introduce our latest Smart Watch..."

### Expected Result:
✅ Consistent product descriptions across languages  
✅ Culturally appropriate copy  
✅ Ready-to-use marketing content  

---

## 🤖 Demo 8: Automated Content Scheduling

**Duration**: ~5 minutes setup, runs automatically

### Scenario:
A blog wants to publish 2 SEO-optimized posts per week automatically.

### Steps:

1. **Navigate to Automation**
   - Go to **ContentCraft AI** → **Automation**

2. **Create Workflow**
   - Click **New Workflow**
   - **Workflow Name**: Weekly Blog Posts
   - **Type**: Scheduled Content Generation

3. **Configure Topics**
   - Add topics or keywords:
     - "WordPress performance tips"
     - "WooCommerce conversion optimization"
     - "WordPress security updates"
     - "Best WordPress themes 2024"
   - Or: **Enable AI Topic Suggestions** (AI suggests trending topics based on your niche)

4. **Set Schedule**
   - **Frequency**: Twice per week
   - **Days**: Monday, Thursday
   - **Time**: 6:00 AM
   - **Timezone**: America/New_York

5. **Content Settings**
   - **Status**: Save as Draft (for review)
   - **Tone**: Professional
   - **Length**: Medium (1000-1500 words)
   - **Language**: English
   - **Enable Brand Voice**: Yes
   - **Categories**: Auto-assign based on topic

6. **Notification**
   - **Email on Generation**: Yes
   - **Recipients**: editor@example.com

7. **Save Workflow**
   - Click **Activate Workflow**
   - Status: Active ✓

8. **Monitor**
   - Dashboard shows:
     - **Next Run**: Monday, Oct 21, 6:00 AM
     - **Topics Remaining**: 4
     - **Posts Generated This Month**: 8
   - Email notification received after each generation
   - Review drafts and publish when ready

### Expected Result:
✅ Consistent content pipeline without manual work  
✅ Never run out of content ideas  
✅ Drafts ready for quick review and publishing  

---

## 📊 Demo 9: Analytics & Usage Dashboard

**Duration**: ~2 minutes

### Scenario:
Review content generation statistics and ROI.

### Steps:

1. **Navigate to Dashboard**
   - Go to **ContentCraft AI** → **Dashboard**

2. **View Overview**
   - **This Month**:
     - Total Generations: 156
     - Blog Posts: 45
     - Products: 89
     - Images Analyzed: 22
     - Total Tokens: 185,000
     - Estimated Cost: $3.70 (based on OpenAI pricing)
     - Time Saved: ~52 hours (vs. manual writing)
   
3. **Charts**
   - Line chart: Daily generations
   - Pie chart: Content types breakdown
   - Bar chart: Most used tones
   - Table: Top performing content (by views)

4. **Recent Activity**
   - 10:30 AM - User "admin" generated product description (#456)
   - 10:15 AM - Bulk generation completed (20 products)
   - 09:45 AM - Brand voice retrained
   - Yesterday 5:00 PM - Scheduled post published

5. **Export Report**
   - Click **Export CSV** for accounting/reporting

### Expected Result:
✅ Clear visibility into usage and costs  
✅ Demonstrate ROI (time saved)  
✅ Identify most productive users/content types  

---

## 🎯 Success Metrics

After implementing ContentCraft AI, expect:

- ⏱️ **90% reduction** in content creation time
- 📈 **3x more content** published per month
- 🎯 **Higher SEO rankings** due to optimized content
- ♿ **Better accessibility** with auto-generated alt-text
- 🎨 **Consistent brand voice** across all content
- 💰 **ROI**: Plugin pays for itself after ~10 product descriptions

---

## 🎥 Video Script (5-minute overview)

1. **Intro (0:00-0:30)**
   - "Struggling to write product descriptions? Let AI help!"
   - Show plugin logo and WordPress dashboard

2. **Content Studio Demo (0:30-1:30)**
   - Fill form, click generate, show results
   - "SEO-optimized blog post in 30 seconds"

3. **WooCommerce Integration (1:30-2:30)**
   - Open product editor, generate description
   - "Complete product content with FAQs and cross-sell"

4. **Bulk Generation (2:30-3:30)**
   - Select 50 products, start processing
   - "Transform your entire catalog in minutes"

5. **Brand Voice (3:30-4:15)**
   - Show training interface
   - "AI learns your unique writing style"

6. **Results (4:15-4:45)**
   - Show before/after metrics
   - "10x productivity, consistent quality"

7. **CTA (4:45-5:00)**
   - "Get ContentCraft AI today"
   - Show link and pricing

---

**These demos can be recorded as video tutorials or used for live presentations.**


