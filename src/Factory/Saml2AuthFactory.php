<?php
/**
 * ZF-SAML
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @copyright 2015 MehrAlsNix (http://www.mehralsnix.de)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://github.com/MehrAlsNix/zf-saml
 */

namespace MehrAlsNix\ZF\SAML\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use MehrAlsNix\ZF\SAML\Controller\Exception;

/**
 * Class Saml2AuthFactory
 * @package MehrAlsNix\ZF\SAML\Factory
 */
class Saml2AuthFactory implements FactoryInterface
{
    private $auth;

    /**
     * @param ServiceLocatorInterface $services
     * @return \OneLogin_Saml2_Auth
     * @throws \MehrAlsNix\ZF\SAML\Controller\Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        if ($this->auth) {
            return $this->auth;
        }

        try {
            return $this->auth = new \OneLogin_Saml2_Auth();
        } catch (\Exception $e) {
            throw new Exception\RuntimeException($e);
        }
    }
}
