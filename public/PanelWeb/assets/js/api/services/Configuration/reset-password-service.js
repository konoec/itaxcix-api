/**
 * Service for user password reset operations
 * Handles password reset functionality for users
 */

class ResetPasswordService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Resets a user's password
     * @param {number} userId - User ID
     * @param {string} newPassword - New password
     * @param {boolean} forcePasswordChange - Force password change on next login (default: true)
     * @param {string} adminReason - Admin reason for the change (optional)
     * @returns {Promise<Object>} Operation result
     */
    static async resetUserPassword(userId, newPassword, forcePasswordChange = true, adminReason = null) {
        try {
            console.log(`🔄 ResetPasswordService: Resetting password for user ${userId}`);
            
            const token = ResetPasswordService.getAuthToken();
            
            if (!token) {
                console.error('❌ Authentication token not found');
                return { success: false, message: 'Authentication token required' };
            }
            
            const requestBody = {
                newPassword,
                forcePasswordChange
            };
            
            // Add admin reason if provided
            if (adminReason) {
                requestBody.adminReason = adminReason;
            }
            
            console.log('📤 Sending password reset request');
            
            const response = await fetch(`${ResetPasswordService.API_BASE_URL}/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(requestBody)
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                let errorMessage = `HTTP Error ${response.status}: ${response.statusText}`;
                
                // Try to read error details
                try {
                    const errorResponse = await response.text();
                    console.log('📄 Error response:', errorResponse);
                    
                    try {
                        const errorDetails = JSON.parse(errorResponse);
                        if (errorDetails.message) {
                            errorMessage = errorDetails.message;
                        }
                    } catch (e) {
                        if (errorResponse) {
                            errorMessage += ` - ${errorResponse}`;
                        }
                    }
                } catch (e) {
                    console.log('⚠️ Could not read error response body');
                }
                
                switch (response.status) {
                    case 400:
                        errorMessage = 'Invalid password - Check format and requirements';
                        break;
                    case 401:
                        errorMessage = 'Unauthorized - Invalid or expired token';
                        break;
                    case 403:
                        errorMessage = 'No permissions to reset password';
                        break;
                    case 404:
                        errorMessage = 'User not found';
                        break;
                    case 422:
                        errorMessage = 'Password does not meet security requirements';
                        break;
                    case 500:
                        errorMessage = 'Internal server error';
                        break;
                }
                
                console.error('❌ Error resetting password:', errorMessage);
                return { success: false, message: errorMessage };
            }
            
            console.log('✅ User password reset successfully');
            return { 
                success: true, 
                message: 'Password updated successfully'
            };
        } catch (error) {
            console.error('❌ Network error resetting password:', error);
            return { 
                success: false, 
                message: 'Connection error resetting password',
                error: error.message
            };
        }
    }
}

// Make available globally
if (typeof window !== 'undefined') {
    window.ResetPasswordService = ResetPasswordService;
}

console.log('✅ ResetPasswordService loaded and available globally');
